<?php
namespace Snowcap\AdminBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Snowcap\AdminBundle\AdminManager;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Yaml\Yaml;

/**
 *
 */
class CatalogueTranslationController extends BaseController
{
    /**
     * @Template()
     */
    public function indexAction($catalogue, $locale)
    {
        /** @var $admin AdminManager */
        $admin = $this->get('snowcap_admin');

        $yaml = new Yaml();

        $activeLocale = $this->getRequest()->get('activeCatalogueLocale');
        if ($activeLocale === null) {
            if ($locale !== null) {
                $activeLocale = $locale;
            } else {
                $activeLocale = $this->getRequest()->getLocale();
            }
        }

        $locales = $this->container->getParameter('locales');
        $fallbackLocale = $locales[0];

        $catalogueReferences = $this->container->getParameter('snowcap_admin.translation_catalogues');

        /** Getting all source catalogues */
        $catalogues = array();
        foreach ($catalogueReferences as $catalogueReference) {
            $explodedCatalog = explode('\\', $catalogueReference);
            $cat = array_pop($explodedCatalog);
            $bundle = array_pop($explodedCatalog);
            $namespace = implode(DIRECTORY_SEPARATOR, $explodedCatalog);
            foreach ($locales as $loc) {
                $filePath = $this->getSourceCataloguePath($namespace, $bundle, $cat, $loc);
                if (file_exists($filePath)) {
                    $catalogues[$cat][$loc] = $yaml->parse(file_get_contents($filePath));
                } else {
                    $catalogues[$cat][$loc] = null;
                }
            }
        }

        /** Setting the active one */
        $activeCatalogue = $catalogues[$catalogue][$activeLocale];

        /** Setting the fallback one */
        if($activeLocale !== $fallbackLocale) {
            $fallbackCatalogue = $this->mergeSourceAndGenerated($catalogue, $catalogues[$catalogue][$fallbackLocale], $fallbackLocale);
        } else {
            $fallbackCatalogue = array();
        }

        /** (re)Generating a yaml with the computed differences from the source catalogue */
        if ($this->getRequest()->getMethod() === 'POST') {
            $data = $this->getRequest()->get('data');
            $diff = $this->compare($data, $activeCatalogue);
            if (!$oldData = @file_get_contents($this->getGeneratedCataloguePath($catalogue,$activeLocale))) {
                $oldData = "{}";
            }
            $logdiff = $this->compare($yaml->parse($oldData), $data);
            file_put_contents(
                $this->getGeneratedCataloguePath($catalogue, $activeLocale),
                $yaml->dump($diff)
            );
            $this->get('snowcap_admin.logger')->logCatalogTranslation($catalogue, $activeLocale, $logdiff);
            $this->clearTranslationsCache();
            $this->setFlash('success','cataloguetranslation.success');
        }


        /** merging after the post to prevent persisting source values in the generated one */
        $activeCatalogue = $this->mergeSourceAndGenerated($catalogue, $activeCatalogue, $activeLocale);

        return array(
            'activeCatalogue' => $activeCatalogue,
            'activeCatalogueName' => $catalogue,
            'activeCatalogueLocale' => $activeLocale,
            'catalogues' => $catalogues,
            'fallbackCatalogue' => $fallbackCatalogue,
            'locales' => $locales,
        );
    }



    /**
     * Merges source and generated catalogue to get the full computed values
     *
     * @param $catalogueName
     * @param $catalogue
     * @param $locale
     * @return array|mixed
     */
    private function mergeSourceAndGenerated($catalogueName, $catalogue, $locale)
    {
        $yaml = new Yaml();
        $filePath = $this->getGeneratedCataloguePath($catalogueName, $locale);
        if (file_exists($filePath)) {
            $generatedYaml = $yaml->parse(file_get_contents($filePath));
            return $this->merge($catalogue, $generatedYaml);
        } else {
            return $catalogue;
        }
    }


    /**
     * Returns an array with the differences of the multi array b compared to multi array a
     *
     * @param array $a
     * @param array $b
     * @return array
     */
    private function compare($a, $b)
    {
        $diff = array();
        foreach ($a as $keyA => $valueA) {
            if ($valueA !== $b[$keyA]) {
                if (is_array($valueA)) {
                    $sub = $this->compare($valueA, $b[$keyA]);
                    if (count($sub) > 0) {
                        $diff[$keyA] = $sub;
                    }
                }
                elseif ($valueA !== '') {
                    $diff[$keyA] = $valueA;
                }
            }
        }
        return $diff;
    }

    /**
     * Merges multidimensionnal arrays
     *
     * @return array|mixed
     */
    private function merge()
    {
        $arrays = func_get_args();
        $base = array_shift($arrays);

        foreach ($arrays as $array) {
            reset($base);
            while (list($key, $value) = @each($array)) {
                if (is_array($value) && @is_array($base[$key])) {
                    $base[$key] = $this->merge($base[$key], $value);
                } else {
                    $base[$key] = $value;
                }
            }
        }

        return $base;
    }

    /**
     * Absolute path getter for source catalogues
     *
     * @param string $namespace
     * @param string $bundle
     * @param string $catalogue
     * @param string $locale
     * @return string
     */
    private function getSourceCataloguePath($namespace, $bundle, $catalogue, $locale)
    {
        return $this->getKernel()->getRootDir() . '/../src/' . $namespace . '/' . $bundle . '/Resources/translations/' . $catalogue . '.' . $locale . '.yml';

    }

    /**
     * Absolute path getter for generated catalogues
     *
     * @param string $catalogue
     * @param string $locale
     * @return string
     */
    private function getGeneratedCataloguePath($catalogue, $locale)
    {
        return $this->getKernel()->getRootDir() . '/Resources/translations/' . $catalogue . '.' . $locale . '.yml';
    }

    protected function clearTranslationsCache()
    {
        $cachedir = $this->getKernel()->getCacheDir() . '/translations';
        /** @var $filesystem \Symfony\Component\Filesystem\Filesystem */
        $filesystem = $this->get('filesystem');
        $filesystem->remove($cachedir);
    }

    /**
     * @return \Symfony\Component\HttpKernel\Kernel;
     */
    protected function getKernel()
    {
        return $this->get('kernel');
    }
}

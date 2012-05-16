<?php
namespace Snowcap\AdminBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
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
        /** @var $admin \Snowcap\AdminBundle\Environment */
        $admin = $this->get('snowcap_admin');

        $yaml = new Yaml();

        if ($locale === null) {
            $locale = $admin->getLocale();
        }

        $locales = $admin->getLocales();
        $catalogueReferences = $admin->getTranslationCatalogues();

        /** Getting all source catalogues */
        $catalogues = array();
        foreach ($catalogueReferences as $catalogueReference) {
            list($namespace, $bundle, $cat) = explode('\\', $catalogueReference);
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
        $activeCatalogue = $catalogues[$catalogue][$locale];

        /** (re)Generating a yaml with the computed differences from the source catalogue */
        if ($this->getRequest()->getMethod() === 'POST') {
            $data = $this->getRequest()->get('data');
            $diff = $this->compare($data, $activeCatalogue);
            file_put_contents(
                $this->getGeneratedCataloguePath($catalogue, $locale),
                $yaml->dump($diff)
            );
            $this->clearTranslationsCache();
            $this->setFlash('success','cataloguetranslation.success');
        }

        /** Merging source and generated catalogue to show the full computed values */
        $filePath = $this->getGeneratedCataloguePath($catalogue, $locale);
        if (file_exists($filePath)) {
            $generatedYaml = $yaml->parse(file_get_contents($filePath));
            $activeCatalogue = $this->merge($activeCatalogue, $generatedYaml);
        }

        return array(
            'activeCatalogue' => $activeCatalogue,
            'activeCatalogueName' => $catalogue,
            'activeCatalogueLocale' => $locale,
            'catalogues' => $catalogues,
        );
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

    private function clearTranslationsCache()
    {
        $cachedir = $this->getKernel()->getCacheDir() . '/translations';
        /** @var $filesystem \Symfony\Component\Filesystem\Filesystem */
        $filesystem = $this->get('filesystem');
        $filesystem->remove($cachedir);
    }

    /**
     * @return \Symfony\Component\HttpKernel\Kernel;
     */
    private function getKernel()
    {
        return $this->get('kernel');
    }

}

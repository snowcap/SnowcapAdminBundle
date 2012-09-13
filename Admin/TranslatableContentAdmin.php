<?php
namespace Snowcap\AdminBundle\Admin;

use Snowcap\AdminBundle\Admin\ContentAdmin;
use Snowcap\AdminBundle\Exception;
use Snowcap\CoreBundle\Entity\TranslatableEntityInterface;
use Snowcap\CoreBundle\Entity\TranslationEntityInterface;
use Doctrine\ORM\Query\Expr;

/**
 * Content admin class
 *
 * Instances of this class are used as configuration for specific models
 */
abstract class TranslatableContentAdmin extends ContentAdmin
{

    /**
     * Return the form for the translation entity
     *
     * @param object $data
     * @return \Symfony\Component\Form\Form
     */
    abstract public function getTranslationForm($data = null);

    public function getQueryBuilder()
    {
        $queryBuilder = parent::getQueryBuilder();
        $activeLocales = $this->environment->getLocales();
        $workingLocale = $this->environment->getWorkingLocale();
        foreach($activeLocales as $activeLocale) {
            $alias = ($activeLocale === $workingLocale) ? 'tr' : 'tr_' . $activeLocale;
            $queryBuilder
                ->addSelect($alias)
                ->leftJoin('e.translations', $alias, Expr\Join::WITH, $alias . '.locale = ' . $queryBuilder->getEntityManager()->getConnection()->quote($activeLocale));
        }

        return $queryBuilder;
    }

    /**
     * Instantiate and return a blank translation entity
     *
     * @param TranslatableEntityInterface $translatedEntity
     * @param string $locale
     * @return object
     */
    public function buildTranslationEntity(TranslatableEntityInterface $translatedEntity, $locale)
    {
        $translationEntityName = $this->getParam('translation_entity_class');
        $translationEntity = new $translationEntityName;
        /* @var TranslationEntityInterface $translationEntity */
        $translationEntity->setTranslatedEntity($translatedEntity);
        $translationEntity->setLocale($locale);
        return $translationEntity;
    }

    /**
     * Find the translation entity for the provided entity and locale
     *
     * @param TranslatableEntityInterface $translatedEntity
     * @param string $locale
     * @return object
     */
    public function findTranslationEntity(TranslatableEntityInterface $translatedEntity, $locale)
    {
        if ($translatedEntity->getTranslations()->containsKey($locale)) {
            return $translatedEntity->getTranslations()->get($locale);
        }
        return $this->buildTranslationEntity($translatedEntity, $locale);
    }

    /**
     * Save the translation entity in the database
     *
     * @param TranslatableEntityInterface $translatedEntity
     * @param TranslationEntityInterface $translationEntity
     */
    public function saveTranslationEntity(TranslatableEntityInterface $translatedEntity, TranslationEntityInterface $translationEntity)
    {
        $translationEntity->setTranslatedEntity($translatedEntity);
        $em = $this->environment->get('doctrine')->getEntityManager();
        $em->persist($translationEntity);
    }

    /**
     * Validate the admin section params
     *
     * @param array $params
     * @throws \Snowcap\AdminBundle\Exception
     */
    public function validateParams(array $params)
    {
        parent::validateParams($params);
        // Checks that there is a valid entity class in the config
        if (!array_key_exists('translation_entity_class', $params)) {
            throw new Exception(sprintf('The admin section %s must be configured with a "translation_entity_class" parameter', $this->getCode()), Exception::ADMIN_INVALID);
        }
        if (!in_array('Snowcap\CoreBundle\Entity\TranslatableEntityInterface', class_implements($params['entity_class']))) {
            throw new Exception(sprintf('The admin section %s "translation_entity_class" parameter (%s) must correspond to a class that implements Snowcap\CoreBundle\Entity\TranslatableEntityInterface', $this->getCode(), $params['entity_class']), Exception::ADMIN_INVALID);
        }
        if (!in_array('Snowcap\CoreBundle\Entity\TranslationEntityInterface', class_implements($params['translation_entity_class']))) {
            throw new Exception(sprintf('The admin section %s "translation_entity_class" parameter (%s) must correspond to a class that implements Snowcap\CoreBundle\Entity\TranslationEntityInterface', $this->getCode(), $params['translation_entity_class']), Exception::ADMIN_INVALID);
        }
    }

    /**
     * Determine if the admin is translatable - false in this case, to be overridden
     *
     * @return bool
     */
    public function isTranslatable()
    {
        return true;
    }

    /**
     * @param object $entity
     */
    public function deleteEntity($entity)
    {
        $em = $this->environment->get('doctrine')->getEntityManager();
        foreach($entity->getTranslations() as $translationEntity) {
            $em->remove($translationEntity);
        }
        parent::deleteEntity($entity);
    }

    public function toString($entity, $locale = null)
    {
        $path = $this->toStringPath();

        if($path === null) {
            return null;
        }

        $output = "empty value";
        if(strpos($path, '%locale%') !== false) {
            $currentLocale = ($locale === null) ? $this->environment->getLocale() : $locale;
            $activeLocales = $this->environment->getLocales();
            $mergedLocales = array_merge(array($currentLocale), array_diff($activeLocales, array($currentLocale)));
            while(!empty($mergedLocales)) {
                $testLocale = array_shift($mergedLocales);
                $propertyPath = new \Symfony\Component\Form\Util\PropertyPath(str_replace('%locale%', $testLocale, $path));
                try {
                    $output = $propertyPath->getValue($entity);
                    break;
                }
                catch(UnexpectedTypeException $e) {
                    // do nothing
                }
            }
        }
        else {
            $propertyPath = new PropertyPath($path);
            $output = $propertyPath->getValue($entity);
        }

        return $output;
    }

}
<?php
namespace Snowcap\AdminBundle\Admin;

use Snowcap\AdminBundle\Admin\ContentAdmin;
use Snowcap\AdminBundle\Exception;
use Snowcap\CoreBundle\Entity\TranslatableEntityInterface;
use Snowcap\CoreBundle\Entity\TranslationEntityInterface;

/**
 * Content admin class
 *
 * Instances of this class are used as configuration for specific models
 */
abstract class TranslatableContentAdmin extends ContentAdmin
{
    abstract public function getTranslationForm($data = null);

    public function buildTranslationEntity(TranslatableEntityInterface $translatedEntity, $locale)
    {
        $translationEntityName = $this->getParam('translation_entity_class');
        $translationEntity = new $translationEntityName;
        /* @var TranslationEntityInterface $translationEntity */
        $translationEntity->setTranslatedEntity($translatedEntity);
        $translationEntity->setLocale($locale);
        return $translationEntity;
    }

    public function attachTranslation(TranslatableEntityInterface $translatedEntity, TranslationEntityInterface $translationEntity)
    {
        $translatedEntity->getTranslations()->set($translationEntity->getLocale(), $translationEntity);
    }

    public function findTranslationEntity(TranslatableEntityInterface $translatedEntity, $locale)
    {
        if($translatedEntity->getTranslations()->containsKey($locale)){
            return $translatedEntity->getTranslations()->get($locale);
        }
        return $this->buildTranslationEntity($translatedEntity, $locale);
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
        if(!in_array('Snowcap\CoreBundle\Entity\TranslatableEntityInterface', class_implements($params['entity_class']))) {
            throw new Exception(sprintf('The admin section %s "translation_entity_class" parameter (%s) must correspond to a class that implements Snowcap\CoreBundle\Entity\TranslatableEntityInterface', $this->getCode(), $params['entity_class']), Exception::ADMIN_INVALID);
        }
        if(!in_array('Snowcap\CoreBundle\Entity\TranslationEntityInterface', class_implements($params['translation_entity_class']))) {
            throw new Exception(sprintf('The admin section %s "translation_entity_class" parameter (%s) must correspond to a class that implements Snowcap\CoreBundle\Entity\TranslationEntityInterface', $this->getCode(), $params['translation_entity_class']), Exception::ADMIN_INVALID);
        }
    }


    public function isTranslatable()
    {
        return true;
    }
}
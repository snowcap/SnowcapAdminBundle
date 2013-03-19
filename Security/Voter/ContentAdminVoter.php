<?php

namespace Snowcap\AdminBundle\Security\Voter;

use Snowcap\AdminBundle\AdminManager;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class ContentAdminVoter implements VoterInterface
{
    private $adminManager;
    /**
     * @param AdminManager $adminManager
     */
    public function __construct(AdminManager $adminManager)
    {
        $this->adminManager = $adminManager;
    }

    /**
     * @param string $attribute
     * @return bool
     */
    public function supportsAttribute($attribute)
    {
        return (0 === strpos($attribute, 'ADMIN_CONTENT_'));
    }

    /**
     * @param string $class
     * @return bool
     */
    public function supportsClass($class)
    {
        return true;
    }

    /**
     * Returns the vote for the given parameters.
     *
     * This method must return one of the following constants:
     * ACCESS_GRANTED, ACCESS_DENIED, or ACCESS_ABSTAIN.
     *
     * @param TokenInterface $token      A TokenInterface instance
     * @param object $object     The object to secure
     * @param array $attributes An array of attributes associated with the method being invoked
     *
     * @return integer either ACCESS_GRANTED, ACCESS_ABSTAIN, or ACCESS_DENIED
     */
    public function vote(TokenInterface $token, $object, array $attributes)
    {
        $result = VoterInterface::ACCESS_ABSTAIN;
        if($this->supportsClass(get_class($token))) {
            foreach ($attributes as $attribute) {
                if (!$this->supportsAttribute($attribute)) {
                    continue;
                }
                list($realAttribute, $adminAlias) = explode('__', $attribute);
                $admin = $this->adminManager->getAdmin(strtolower($adminAlias));
                $result = $admin->isGranted($token->getUser(), $realAttribute, $object);
            }
        }

        return $result;
    }
}
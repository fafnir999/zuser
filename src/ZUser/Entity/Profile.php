<?php
/**
 * Author: Yaroslav Kovalev
 * Date: 03.05.2016
 * Time: 12:14
 */

namespace ZUser\Entity;

// Use block for ZF2
use Doctrine\ORM\Mapping as ORM,
    Gedmo\Mapping\Annotation as Gedmo,
    Doctrine\Common\Collections\Collection,
    Doctrine\Common\Collections\ArrayCollection;

/**
 * ZUser\Entity\Profile
 *
 * @ORM\Entity
 * @ORM\Table(name="zuser_profile")
 */
class Profile
{
    //TODO перенести в конкретный тип приложения
    const ACCOUNT_TYPE_RENTER = 'renter';
    const ACCOUNT_TYPE_LISTER = 'lister';

    /**
     * @ORM\Id
     * @ORM\Column(type="integer");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $name;

    /**
     * @ORM\Column(type="string", columnDefinition="CHAR(255) NOT NULL")
     */
    protected $subscriber_email;

    /**
     * @ORM\OneToOne(targetEntity="Account", inversedBy="profile")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="account_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    protected $account;

    //TODO Следующие свойства перенести в конткретный класс приложения
    /**
     * @ORM\Column(type="string", nullable=false)
     */
    protected $profile_type;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $listerCondition;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $renterCondition;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getSubscriberEmail()
    {
        return $this->subscriber_email;
    }

    /**
     * @param mixed $subscriber_email
     */
    public function setSubscriberEmail($subscriber_email)
    {
        $this->subscriber_email = $subscriber_email;
    }

    /**
     * Set account
     *
     * @param Account $account
     * @return Profile
     */
    public function setAccount(Account $account = null)
    {
        $this->account = $account;
        return $this;
    }

    /**
     * Get account
     *
     * @return Account
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * @return mixed
     */
    public function getProfileType()
    {
        return $this->profile_type;
    }

    /**
     * @param mixed $profile_type
     */
    public function setProfileType($profile_type)
    {
        $this->profile_type = $profile_type;
    }

    /**
     * @return mixed
     */
    public function getListerCondition()
    {
        return $this->listerCondition;
    }

    /**
     * @param mixed $listerCondition
     */
    public function setListerCondition($listerCondition)
    {
        $this->listerCondition = $listerCondition;
    }

    /**
     * @return mixed
     */
    public function getRenterCondition()
    {
        return $this->renterCondition;
    }

    /**
     * @param mixed $renterCondition
     */
    public function setRenterCondition($renterCondition)
    {
        $this->renterCondition = $renterCondition;
    }
}
<?php
namespace ZUser\Entity;

// Use block for ZF2
use Doctrine\ORM\Mapping as ORM,
    Gedmo\Mapping\Annotation as Gedmo,
    Doctrine\Common\Collections\Collection,
    Doctrine\Common\Collections\ArrayCollection;


/**
 * ZUser\Entity\Account
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="zuser_account", uniqueConstraints={@ORM\UniqueConstraint(name="unique_email", columns={"email"})})
 * @ORM\Entity(repositoryClass="ZUser\Entity\Repository\AccountRepository")
 */
class Account
{
    const ACCOUNT_NOT_APPROVED = 0;
    const ACCOUNT_APPROVED = 1;

    const ACCOUNT_DISABLED = 0;
    const ACCOUNT_ENABLED = 1;

    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="email", type="string", columnDefinition="CHAR(255) NOT NULL")
     */
    protected $email;

    /**
     * @ORM\Column(name="password", type="string", columnDefinition="CHAR(50) NOT NULL")
     */
    protected $password;

    /**
     * @ORM\Column(name="salt", type="string", columnDefinition="CHAR(50) NOT NULL")
     */
    protected $salt;

    /**
     * @ORM\Column(name="hash", type="string", columnDefinition="CHAR(50) NOT NULL")
     */
    protected $hash;

    /**
     * @ORM\Column(name="recovery_token", type="string", columnDefinition="CHAR(50)", nullable=true)
     */
    protected $recovery_token;

    /**
     * @ORM\Column(name="recovery_token_expires", type="datetime", nullable=true)
     */
    protected $recovery_token_expires;

    /**
     * @ORM\Column(name="provider", type="string", columnDefinition="CHAR(50)", nullable=true)
     */
    protected $provider;

    /**
     * @ORM\Column(name="provider_id", type="string", columnDefinition="CHAR(50)", nullable=true)
     */
    protected $provider_id;

    /**
     * @ORM\Column(name="approved", type="boolean", options={"default" = 0})
     */
    protected $approved;

    /**
     * @ORM\Column(name="approve_token", type="string", columnDefinition="CHAR(50)", nullable=true)
     */
    protected $approve_token;

    /**
     * @ORM\Column(name="approve_token_expires", type="datetime", nullable=true)
     */
    protected $approve_token_expires;

    /**
     * @ORM\Column(name="enabled", type="boolean", options={"default" = 1})
     */
    protected $enabled;

    /**
     * @ORM\Column(type="integer", options={"unsigned"=true});
     */
    protected $ip;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="date_created", type="datetime")
     */
    protected $date_created;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="date_modified", type="datetime")
     */
    protected $date_modified;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $date_last_login;

    /**
     * @ORM\OneToOne(targetEntity="Profile", mappedBy="account")
     */
    protected $profile;

    /**
     * Сохранение в базу данных ip адреса. Преобразуется в тип int
     *
     * @ORM\PrePersist
     */
    public function updateIp()
    {
        $remote = new \Zend\Http\PhpEnvironment\RemoteAddress;
        $ip = $remote->getIpAddress();
        //На всякий случай фильтрация ip, чтобы он соответствовал ip4 адресу
        if(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $this->setIp( sprintf('%u', ip2long($ip)) );
        }
    }

    // ----------------------- After that line goes auto-generated methods (setters and getters) -----------------------

    /**
     * Constructor
     */
    public function __construct()
    {
//        $this->properties = new \Doctrine\Common\Collections\ArrayCollection();
//        $this->contracts = new \Doctrine\Common\Collections\ArrayCollection();
//        $this->reviews = new \Doctrine\Common\Collections\ArrayCollection();
//        $this->wish_list = new \Doctrine\Common\Collections\ArrayCollection();
//        $this->transactions_pending = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Account
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return Account
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set salt
     *
     * @param string $salt
     * @return Account
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * Get salt
     *
     * @return string 
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * Set hash
     *
     * @param string $hash
     * @return Account
     */
    public function setHash($hash)
    {
        $this->hash = $hash;

        return $this;
    }

    /**
     * Get hash
     *
     * @return string 
     */
    public function getHash()
    {
        return $this->hash;
    }


    /**
     * Set recovery_token
     *
     * @param string $recoveryToken
     * @return Account
     */
    public function setRecoveryToken($recoveryToken)
    {
        $this->recovery_token = $recoveryToken;

        return $this;
    }

    /**
     * Get recovery_token
     *
     * @return string 
     */
    public function getRecoveryToken()
    {
        return $this->recovery_token;
    }

    /**
     * Set recovery_token_expires
     *
     * @param \DateTime $recoveryTokenExpires
     * @return Account
     */
    public function setRecoveryTokenExpires($recoveryTokenExpires)
    {
        $this->recovery_token_expires = $recoveryTokenExpires;

        return $this;
    }

    /**
     * Get recovery_token_expires
     *
     * @return \DateTime 
     */
    public function getRecoveryTokenExpires()
    {
        return $this->recovery_token_expires;
    }

    /**
     * Set provider
     *
     * @param string $provider
     * @return Account
     */
    public function setProvider($provider)
    {
        $this->provider = $provider;

        return $this;
    }

    /**
     * Get provider
     *
     * @return string 
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * Set provider_id
     *
     * @param string $providerId
     * @return Account
     */
    public function setProviderId($providerId)
    {
        $this->provider_id = $providerId;

        return $this;
    }

    /**
     * Get provider_id
     *
     * @return string 
     */
    public function getProviderId()
    {
        return $this->provider_id;
    }

    /**
     * Set approved
     *
     * @param boolean $approved
     * @return Account
     */
    public function setApproved($approved)
    {
        $this->approved = $approved;

        return $this;
    }

    /**
     * Get approved
     *
     * @return boolean 
     */
    public function getApproved()
    {
        return $this->approved;
    }

    /**
     * Set approve_token
     *
     * @param string $approveToken
     * @return Account
     */
    public function setApproveToken($approveToken)
    {
        $this->approve_token = $approveToken;

        return $this;
    }

    /**
     * Get approve_token
     *
     * @return string 
     */
    public function getApproveToken()
    {
        return $this->approve_token;
    }

    /**
     * Set approve_token_expires
     *
     * @param \DateTime $approveTokenExpires
     * @return Account
     */
    public function setApproveTokenExpires($approveTokenExpires)
    {
        $this->approve_token_expires = $approveTokenExpires;

        return $this;
    }

    /**
     * Get approve_token_expires
     *
     * @return \DateTime 
     */
    public function getApproveTokenExpires()
    {
        return $this->approve_token_expires;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     * @return Account
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get enabled
     *
     * @return boolean 
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    public function setIp($ip)
    {
        $this->ip = $ip;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIp()
    {
        return $this->ip !== 0 ? long2ip((float)$this->ip) : 'undefined';
    }

    /**
     * Set date_created
     *
     * @param \DateTime $dateCreated
     * @return Account
     */
    public function setDateCreated($dateCreated)
    {
        $this->date_created = $dateCreated;

        return $this;
    }

    /**
     * Get date_created
     *
     * @return \DateTime 
     */
    public function getDateCreated()
    {
        return $this->date_created;
    }

    /**
     * Set date_modified
     *
     * @param \DateTime $dateModified
     * @return Account
     */
    public function setDateModified($dateModified)
    {
        $this->date_modified = $dateModified;

        return $this;
    }

    /**
     * Get date_modified
     *
     * @return \DateTime 
     */
    public function getDateModified()
    {
        return $this->date_modified;
    }

    /**
     * @return mixed
     */
    public function getDateLastLogin()
    {
        return  $this->date_last_login;
    }

    /**
     * @param mixed $date_last_login
     */
    public function setDateLastLogin($date_last_login)
    {
        $this->date_last_login = $date_last_login;
    }

    /**
     * Set profile
     *
     * @param Profile $profile
     * @return Profile
     */
    public function setProfile(Profile $profile= null)
    {
        $this->profile = $profile;
        return $this;
    }

    /**
     * Get profile
     *
     * @return Profile
     */
    public function getProfile()
    {
        return $this->profile;
    }
}

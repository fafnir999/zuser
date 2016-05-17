<?php
namespace ZUser\Model\Validator;

// Use block for ZF2
use Zend\Validator\AbstractValidator;


class CheckEmail extends AbstractValidator
{
    const ALREADY_EXISTS = 1;
    const NOT_EXISTS = 2;

    protected $messageTemplates = [
        self::ALREADY_EXISTS => "Account with email '%value%' already exist",
        self::NOT_EXISTS => "Account with email '%value%' does not exist",
    ];

    /**
     * Проверяет email на существование или отсутствие в таблице базы данных account
     * При опции $existWrong == true ошибка выбрасывается при сушествовании email,
     * При опции $existWrong == false ошибка выбрасывается при отсутствии email
     *
     * @param mixed $value
     * @return bool
     */
    public function isValid($value)
    {
        $this->setValue($value);

        /** @var \Doctrine\ORM\EntityManager $entity_manager */
        $entity_manager = $this->getOption('_entityManager');

        $existWrong = $this->getOption('existWrong');

        /** @var \ZUser\Entity\Account $account */
        $account = $entity_manager->getRepository($this->getOption('accountClass'))->findOneBy([$this->getOption('field') => $value]);
        if ($existWrong && !is_null($account) ) {
            $this->error(self::ALREADY_EXISTS, $value);
            return false;
        } elseif(!$existWrong && !is_null($account)) {
            return true;
        } elseif($existWrong && is_null($account)) {
            return true;
        } elseif(!$existWrong && is_null($account)) {
            $this->error(self::NOT_EXISTS, $value);
            return false;
        }

        return true;
    }


    /**
     * Sets validator options
     *
     * @param  int|array|\Traversable $options
     */
    public function __construct($options = array())
    {
        parent::__construct($options);
        $translator = $this->getOption('translator');
        self::setTranslator($translator);
    }
}

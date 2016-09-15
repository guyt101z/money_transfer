<?php

namespace entities;

use entities\types\DocumentType;

class Document extends BaseEntity
{
    const STATUS_CREATED = 'created';
    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_ERROR = 'error';

    /**
     * @var array
     */
    private statuc $possibleStatuses = [
        self::STATUS_CREATED,
        self::STATUS_PENDING,
        self::STATUS_COMPLETED,
        self::STATUS_ERROR,
    ];

    /**
     * @var int
     */
    private $id;

    /**
     * @var \DateTime|null
     */
    private $createdAt;

    /**
     * @var \DateTime
     */
    private $executedAt;

    /**
     * @var string
     */
    private $status;

    /**
     * @var DocumentType
     */
    private $type;

    /**
     * @var User
     */
    private $creator;

    /**
     * @var User|null
     */
    private $executor;

    /**
     * @var array
     */
    private $context = [];

    /**
     * @var string
     */
    private $notice;

    public function rollback()
    {
        try {
            $this->type->backward($this);
            $this->setStatus(self::STATUS_PENDING);
        } catch (\Exception $e) {
            $this->setStatus(self::STATUS_ERROR);
            $this->setNotice($e->getMessage());
        }
    }

    /**
     * @param User $executor
     */
    public function execute(User $executor)
    {
        $this->setExecutedAt(new \DateTime());
        $this->setExecutor($executor);

        try {
            $this->type->forward($this);
            $this->setStatus(self::STATUS_COMPLETED);
        } catch (\Exception $e) {
            $this->setStatus(self::STATUS_ERROR);
            $this->setNotice($e->getMessage());
        }
    }

    /**
     * @inheritdoc
     */
    public function validate()
    {
        if (!in_array($this->status, self::$possibleStatuses)) {
            $this->errors[] = 'Wrong status given';
        }

        if (!$this->type) {
            $this->errors[] = 'Type of the document is empty or missing';
        }

        if (!$this->creator) {
            $this->errors[] = 'Creator of the document is empty or missing';
        }

        return empty($this->errors);
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return \DateTime|null
     */
    public function getExecutedAt()
    {
        return $this->executedAt;
    }

    /**
     * @return User
     */
    public function getCreator()
    {
        return $this->creator;
    }

    /**
     * @return User|null
     */
    public function getExecutor()
    {
        return $this->executor;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return DocumentType
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return array
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @return string
     */
    public function getNotice()
    {
        return $this->notice;
    }

    /**
     * @inheritdoc
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @param \DateTime|null $executedAt
     */
    public function setExecutedAt(\DateTime $executedAt = null)
    {
        $this->executedAt = $executedAt;
    }

    /**
     * @param User $creator
     */
    public function setCreator(User $creator)
    {
        $this->creator = $creator;
    }

    /**
     * @param User|null $executor
     */
    public function setExecutor(User $executor = null)
    {
        $this->executor = $executor;
    }

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @param DocumentType $type
     */
    public function setType(DocumentType $type)
    {
        $this->type = $type;
    }

    /**
     * @param array $context
     */
    public function setContext(array $context)
    {
        $this->context = $context;
    }

    /**
     * @param string $notice
     */
    public function setNotice($notice)
    {
        $this->notice = $notice;
    }

    /**
     * @inheritdoc
     */
    public function load(array $map)
    {
        if (isset($map['id'])) {
            $this->setId((int)$map['id']);
        }

        if (isset($map['createdAt'])) {
            $this->setCreatedAt(new \DateTime($map['createdAt']));
        }

        if (isset($map['executedAt'])) {
            $this->setExecutedAt(new \DateTime($map['executedAt']));
        }

        if (isset($map['status'])) {
            $this->setStatus($map['status']);
        }

        if (isset($map['notice'])) {
            $this->setNotice($map['notice']);
        }

        if (isset($map['context'])) {
            $context = json_decode($map['context']);

            if (!json_last_error()) {
                $this->setContext((array)$context);
            }
        }
    }

    /**
     * @return array
     */
    public function toMap()
    {
        return [
            'id' => $this->getId(),
            'createdAt' => $this->getCreatedAt()->format(DATE_W3C),
            'executedAt' => $this->getExecutedAt() ? $this->getExecutedAt()->format(DATE_W3C) : null,
            'status' => $this->getStatus(),
            'context' => $this->getContext(),
            'notice' => $this->getNotice(),
            'type' => $this->getType()->getName(),
            'creatorId' => $this->getCreator()->getId(),
            'executorId' => $this->getExecutor() ? $this->getExecutor()->getId() : null,
        ];
    }
}
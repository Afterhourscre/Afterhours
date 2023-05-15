<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the commercial license
 * that is bundled with this package in the file LICENSE.txt.
 *
 * @category Extait
 * @package Extait_Cookie
 * @copyright Copyright (c) 2016-2018 Extait, Inc. (http://www.extait.com)
 */

namespace Extait\Cookie\Api\Data;

/** @api */
interface CategoryInterface
{
    /**
     * The keys of data array.
     */
    const ID = 'id';
    const NAME = 'name';
    const DESCRIPTION = 'description';
    const IS_SYSTEM = 'is_system';

    /**
     * Get ID.
     *
     * @return int
     */
    public function getId();

    /**
     * Get Name.
     *
     * @return string
     */
    public function getName();

    /**
     * Get Description.
     *
     * @return string
     */
    public function getDescription();

    /**
     * @return bool
     */
    public function getIsSystem();

    /**
     * Set ID.
     *
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * Set Name.
     *
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * Set Description.
     *
     * @param string $description
     * @return $this
     */
    public function setDescription($description);

    /**
     * @param bool $isSystem
     * @return $this
     */
    public function setIsSystem($isSystem);
}

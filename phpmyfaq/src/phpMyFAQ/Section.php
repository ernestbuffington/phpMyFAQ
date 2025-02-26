<?php

/**
 * The section class provides sections
 *
 * This Source Code Form is subject to the terms of the Mozilla Public License,
 * v. 2.0. If a copy of the MPL was not distributed with this file, You can
 * obtain one at https://mozilla.org/MPL/2.0/.
 *
 * @package   phpMyFAQ
 * @author    Timo Wolf <amna.wolf@gmail.com>
 * @copyright 2018-2023 phpMyFAQ Team
 * @license   https://www.mozilla.org/MPL/2.0/ Mozilla Public License Version 2.0
 * @link      https://www.phpmyfaq.de
 * @since     2018-07-19
 */

namespace phpMyFAQ;

/**
 * Class Section
 *
 * @package phpMyFAQ
 */
class Section
{
    /**
     * Constructor.
     */
    public function __construct(private Configuration $config)
    {
    }

    /**
     * Adds a new section entry.
     *
     * @param string $name Name of the section
     * @param string $description Description of the category
     */
    public function addSection(string $name, string $description): int
    {
        $id = $this->config->getDb()->nextId(Database::getTablePrefix() . 'faqsections', 'id');

        $query = sprintf(
            "
            INSERT INTO
                %sfaqsections
            (id, name, description)
                VALUES
            (%d, '%s', '%s')",
            Database::getTablePrefix(),
            $id,
            $name,
            $description
        );
        $this->config->getDb()->query($query);

        return $id;
    }

    /**
     * Gets one section by id.
     *
     * @return string[]
     */
    public function getSection(int $sectionId): array
    {
        $query = sprintf(
            "SELECT * FROM %sfaqsections WHERE id = %d",
            Database::getTablePrefix(),
            $sectionId
        );

        $res = $this->config->getDb()->query($query);

        if ($res) {
            return $this->config->getDb()->fetchArray($res);
        }

        return [];
    }

    /**
     * Get all sections.
     *
     * @return string[]
     */
    public function getAllSections(): array
    {
        $query = sprintf('SELECT id, name, description FROM %sfaqsections', Database::getTablePrefix());
        $res = $this->config->getDb()->query($query);

        if ($res) {
            return $this->config->getDb()->fetchAll($res);
        }

        return [];
    }

    /**
     * updates a section entry.
     *
     * @param int    $id Id of the section to edit
     * @param string $name Name of the section
     * @param string $description Description of the category
     */
    public function updateSection(int $id, string $name, string $description): bool
    {
        $update = sprintf(
            "UPDATE %sfaqsections (name, description) VALUES ('%s', '%s') WHERE id = %d",
            Database::getTablePrefix(),
            $name,
            $description,
            $id
        );

        $res = $this->config->getDb()->query($update);
        if (!$res) {
            return false;
        }

        return true;
    }

    /**
     * deletes a section entry.
     *
     * @param int $id Id of the section to edit
     */
    public function deleteSection($id): bool
    {
        $delete = sprintf(
            "
            DELETE FROM
                %sfaqsections
            WHERE id = %d
            ",
            Database::getTablePrefix(),
            $id
        );

        $res = $this->config->getDb()->query($delete);
        if (!$res) {
            return false;
        }

        $delete = sprintf(
            "
            DELETE FROM
                %sfaqsection_category
            WHERE section_id = %d
            ",
            Database::getTablePrefix(),
            $id
        );

        $res = $this->config->getDb()->query($delete);
        if (!$res) {
            return false;
        }

        $delete = sprintf(
            "
            DELETE FROM
                %sfaqsection_user
            WHERE section_id = %d
            ",
            Database::getTablePrefix(),
            $id
        );

        $res = $this->config->getDb()->query($delete);
        if (!$res) {
            return false;
        }

        $delete = sprintf(
            "
            DELETE FROM
                %sfaqsection_group
            WHERE section_id = %d
            ",
            Database::getTablePrefix(),
            $id
        );

        $res = $this->config->getDb()->query($delete);
        if (!$res) {
            return false;
        }

        $delete = sprintf(
            "
            DELETE FROM
                %sfaqsection_right
            WHERE section_id = %d
            ",
            Database::getTablePrefix(),
            $id
        );

        $res = $this->config->getDb()->query($delete);
        if (!$res) {
            return false;
        }

        $delete = sprintf(
            "
            DELETE FROM
                %sfaqsection_news
            WHERE section_id = %d
            ",
            Database::getTablePrefix(),
            $id
        );

        $res = $this->config->getDb()->query($delete);
        if (!$res) {
            return false;
        }

        return true;
    }

    /**
     * adds a section - category relation
     *
     * @param int $sectionId Id of the section
     * @param int $categoryId Id of the category
     * @return bool
     */
    public function addSectionCategory($sectionId, $categoryId)
    {
        $insert = sprintf(
            "
            INSERT INTO
                %sfaqsection_category
            (sectionId, categoryId)
                VALUES
            (%d, %d)
            ",
            Database::getTablePrefix(),
            $sectionId,
            $categoryId
        );

        $res = $this->config->getDb()->query($insert);
        if (!$res) {
            return false;
        }

        return true;
    }

    /**
     * removes a section - category relation
     *
     * @param int $sectionId Id of the section
     * @param int $categoryId Id of the category
     * @return bool
     */
    public function removeSectionCategory($sectionId, $categoryId)
    {
        $delete = sprintf(
            "
            DELETE FROM
                %sfaqsection_category
            WHERE 
                sectionId = %d AND categoryId = %d
            ",
            Database::getTablePrefix(),
            $sectionId,
            $categoryId
        );

        $res = $this->config->getDb()->query($delete);
        if (!$res) {
            return false;
        }

        return true;
    }

    /**
     * adds a section - user relation
     *
     * @param int $sectionId Id of the section
     * @param int $userId Id of the user
     * @return bool
     */
    public function addSectionuser($sectionId, $userId)
    {
        $insert = sprintf(
            "
            INSERT INTO
                %sfaqsection_user
            (sectionId, userId)
                VALUES
            (%d, %d)
            ",
            Database::getTablePrefix(),
            $sectionId,
            $userId
        );

        $res = $this->config->getDb()->query($insert);
        if (!$res) {
            return false;
        }

        return true;
    }

    /**
     * removes a section - user relation
     *
     * @param int $sectionId Id of the section
     * @param int $userId Id of the user
     * @return bool
     */
    public function removeSectionUser($sectionId, $userId)
    {
        $delete = sprintf(
            "
            DELETE FROM
                %sfaqsection_user
            WHERE 
                sectionId = %d AND userId = %d
            ",
            Database::getTablePrefix(),
            $sectionId,
            $userId
        );

        $res = $this->config->getDb()->query($delete);
        if (!$res) {
            return false;
        }

        return true;
    }

    /**
     * adds a section - group relation
     *
     * @param int $sectionId Id of the section
     * @param int $groupId Id of the group
     * @return bool
     */
    public function addSectionGroup($sectionId, $groupId)
    {
        $insert = sprintf(
            "
            INSERT INTO
                %sfaqsection_group
            (sectionId, groupId)
                VALUES
            (%d, %d)
            ",
            Database::getTablePrefix(),
            $sectionId,
            $groupId
        );

        $res = $this->config->getDb()->query($insert);
        if (!$res) {
            return false;
        }

        return true;
    }

    /**
     * removes a section - group relation
     *
     * @param int $sectionId Id of the section
     * @param int $groupId Id of the group
     * @return bool
     */
    public function removeSectionGroup($sectionId, $groupId)
    {
        $delete = sprintf(
            "
            DELETE FROM
                %sfaqsection_group
            WHERE 
                sectionId = %d AND groupId = %d
            ",
            Database::getTablePrefix(),
            $sectionId,
            $groupId
        );

        $res = $this->config->getDb()->query($delete);
        if (!$res) {
            return false;
        }

        return true;
    }

    /**
     * adds a section - news relation
     *
     * @param int $sectionId Id of the section
     * @param int $newsId Id of the news
     * @return bool
     */
    public function addSectionNews($sectionId, $newsId)
    {
        $insert = sprintf(
            "
            INSERT INTO
                %sfaqsection_news
            (sectionId, newsId)
                VALUES
            (%d, %d)
            ",
            Database::getTablePrefix(),
            $sectionId,
            $newsId
        );

        $res = $this->config->getDb()->query($insert);
        if (!$res) {
            return false;
        }

        return true;
    }

    /**
     * removes a section - news relation
     *
     * @param int $sectionId Id of the section
     * @param int $newsId Id of the news
     * @return bool
     */
    public function removeSectionNews($sectionId, $newsId)
    {
        $delete = sprintf(
            "
            DELETE FROM
                %sfaqsection_news
            WHERE 
                sectionId = %d AND newsId = %d
            ",
            Database::getTablePrefix(),
            $sectionId,
            $newsId
        );

        $res = $this->config->getDb()->query($delete);
        if (!$res) {
            return false;
        }

        return true;
    }
}

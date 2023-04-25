<?php

declare(strict_types=1);

/*
 * This file is part of the "Lockdown per User bundle" for Kimai.
 * All rights reserved by Kevin Papst (www.kevinpapst.de).
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace LockdownPerUserBundle\Migrations;

use App\Doctrine\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * @version 2.0
 */
final class Version20993112235959 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Fix user preference names';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("UPDATE kimai2_user_preferences SET `name` = 'lockdown_period_start' WHERE `name` = 'timesheet.rules.lockdown_period_start'");
        $this->addSql("UPDATE kimai2_user_preferences SET `name` = 'lockdown_period_end' WHERE `name` = 'timesheet.rules.lockdown_period_end'");
        $this->addSql("UPDATE kimai2_user_preferences SET `name` = 'lockdown_period_timezone' WHERE `name` = 'timesheet.rules.lockdown_period_timezone'");
        $this->addSql("UPDATE kimai2_user_preferences SET `name` = 'lockdown_grace_period' WHERE `name` = 'timesheet.rules.lockdown_grace_period'");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("UPDATE kimai2_user_preferences SET `name` = 'timesheet.rules.lockdown_period_start' WHERE `name` = 'lockdown_period_start'");
        $this->addSql("UPDATE kimai2_user_preferences SET `name` = 'timesheet.rules.lockdown_period_end' WHERE `name` = 'lockdown_period_end'");
        $this->addSql("UPDATE kimai2_user_preferences SET `name` = 'timesheet.rules.lockdown_period_timezone' WHERE `name` = 'lockdown_period_timezone'");
        $this->addSql("UPDATE kimai2_user_preferences SET `name` = 'timesheet.rules.lockdown_grace_period' WHERE `name` = 'lockdown_grace_period'");
    }
}

<?php

/*
 * This file is part of the LockdownPerUserBundle.
 * All rights reserved by Kevin Papst (www.kevinpapst.de).
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace KimaiPlugin\LockdownPerUserBundle\EventSubscriber;

use App\Configuration\SystemConfiguration;
use App\Entity\UserPreference;
use App\Event\UserPreferenceEvent;
use App\Form\Type\TimezoneType;
use App\Validator\Constraints\DateTimeFormat;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class UserPreferenceSubscriber implements EventSubscriberInterface
{
    public function __construct(private SystemConfiguration $systemConfiguration, private AuthorizationCheckerInterface $voter)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            UserPreferenceEvent::class => ['loadUserPreferences', 200],
        ];
    }

    public function loadUserPreferences(UserPreferenceEvent $event): void
    {
        if (!$this->voter->isGranted('lockdown_per_user')) {
            return;
        }

        $lockdownStartHelp = null;
        $lockdownEndHelp = null;
        $lockdownGraceHelp = null;
        $dateFormat = 'D, d M Y H:i:s';

        if ($this->systemConfiguration->isTimesheetLockdownActive()) {
            $userTimezone = new \DateTimeZone($event->getUser()->getTimezone());
            $timezone = $event->getUser()->getPreferenceValue('lockdown_period_timezone');

            if ($timezone !== null) {
                $timezone = new \DateTimeZone((string) $timezone);
            }

            if ($timezone === null) {
                $timezone = $userTimezone;
            }

            try {
                $lockdownPeriodStart = $event->getUser()->getPreferenceValue('lockdown_period_start');
                if ($lockdownPeriodStart !== null) {
                    $lockdownStartHelp = new \DateTime((string) $lockdownPeriodStart, $timezone);
                    $lockdownStartHelp->setTimezone($userTimezone);
                    $lockdownStartHelp = $lockdownStartHelp->format($dateFormat);
                }

                $lockdownPeriodEnd = $event->getUser()->getPreferenceValue('lockdown_period_end');
                if ($lockdownPeriodEnd !== null) {
                    $lockdownEndHelp = new \DateTime((string) $lockdownPeriodEnd, $timezone);
                    $lockdownGrace = $event->getUser()->getPreferenceValue('lockdown_grace_period');
                    if ($lockdownGrace !== null) {
                        $lockdownGraceHelp = clone $lockdownEndHelp;
                        $lockdownGraceHelp->modify((string) $lockdownGrace);
                        $lockdownGraceHelp->setTimezone($userTimezone);
                        $lockdownGraceHelp = $lockdownGraceHelp->format($dateFormat);
                    }
                    $lockdownEndHelp->setTimezone($userTimezone);
                    $lockdownEndHelp = $lockdownEndHelp->format($dateFormat);
                }
            } catch (\Exception $ex) {
                $lockdownStartHelp = 'invalid';
            }
        }

        $event->addPreference(
            (new UserPreference('lockdown_period_start'))
                ->setOrder(2000)
                ->setType(TextType::class)
                ->setSection('LockdownPerUser')
                ->setOptions(['help' => $lockdownStartHelp, 'translation_domain' => 'system-configuration', 'label' => 'label.timesheet.rules.lockdown_period_start'])
                ->addConstraint(new DateTimeFormat())
        );

        $event->addPreference(
            (new UserPreference('lockdown_period_end'))
                ->setOrder(2010)
                ->setType(TextType::class)
                ->setSection('LockdownPerUser')
                ->setOptions(['help' => $lockdownEndHelp, 'translation_domain' => 'system-configuration', 'label' => 'label.timesheet.rules.lockdown_period_end'])
                ->addConstraint(new DateTimeFormat())
        );

        $event->addPreference(
            (new UserPreference('lockdown_period_timezone'))
                ->setOrder(2020)
                ->setType(TimezoneType::class)
                ->setSection('LockdownPerUser')
                ->setOptions(['translation_domain' => 'system-configuration', 'label' => 'label.timesheet.rules.lockdown_period_timezone'])
        );

        $event->addPreference(
            (new UserPreference('lockdown_grace_period'))
                ->setOrder(2030)
                ->setType(TextType::class)
                ->setSection('LockdownPerUser')
                ->setOptions(['help' => $lockdownGraceHelp, 'translation_domain' => 'system-configuration', 'label' => 'label.timesheet.rules.lockdown_grace_period'])
                ->addConstraint(new DateTimeFormat())
        );
    }
}

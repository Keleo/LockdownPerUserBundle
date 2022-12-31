<?php

/*
 * This file is part of the LockdownPerUserBundle for Kimai.
 * All rights reserved by Kevin Papst (www.kevinpapst.de).
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace KimaiPlugin\LockdownPerUserBundle\EventSubscriber;

use App\Configuration\SystemConfiguration;
use App\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserConfigurationSubscriber implements EventSubscriberInterface
{
    public function __construct(private TokenStorageInterface $storage, private SystemConfiguration $systemConfiguration)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => ['prepareUserProfileSettings', 200]
        ];
    }

    public function prepareUserProfileSettings(KernelEvent $event): void
    {
        // ignore sub-requests
        if (!$event->isMainRequest()) {
            return;
        }

        // ignore events like the toolbar where we do not have a token
        if (null === ($token = $this->storage->getToken())) {
            return;
        }

        $user = $token->getUser();

        if ($user instanceof User) {
            $start = $user->getPreferenceValue('lockdown_period_start');
            if ($start === null) {
                return;
            }
            $this->systemConfiguration->offsetSet('timesheet.rules.lockdown_period_start', $start);

            $end = $user->getPreferenceValue('lockdown_period_end');
            $this->systemConfiguration->offsetSet('timesheet.rules.lockdown_period_end', $end);

            $grace = $user->getPreferenceValue('lockdown_grace_period');
            $this->systemConfiguration->offsetSet('timesheet.rules.lockdown_grace_period', $grace);

            $timezone = $user->getPreferenceValue('lockdown_period_timezone');
            $this->systemConfiguration->offsetSet('timesheet.rules.lockdown_period_timezone', $timezone);
        }
    }
}

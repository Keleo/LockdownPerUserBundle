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
use App\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserConfigurationSubscriber implements EventSubscriberInterface
{
    private $storage;
    private $configurations;

    public function __construct(TokenStorageInterface $storage, SystemConfiguration $systemConfiguration)
    {
        $this->storage = $storage;
        $this->configurations = $systemConfiguration;
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
        if (!$event->isMasterRequest()) {
            return;
        }

        // ignore events like the toolbar where we do not have a token
        if (null === ($token = $this->storage->getToken())) {
            return;
        }

        $user = $token->getUser();

        if ($user instanceof User) {
            $start = $user->getPreferenceValue('timesheet.rules.lockdown_period_start');
            if ($start === null) {
                return;
            }
            $this->configurations->offsetSet('timesheet.rules.lockdown_period_start', $start);

            $end = $user->getPreferenceValue('timesheet.rules.lockdown_period_end');
            $this->configurations->offsetSet('timesheet.rules.lockdown_period_end', $end);

            $grace = $user->getPreferenceValue('timesheet.rules.lockdown_grace_period');
            $this->configurations->offsetSet('timesheet.rules.lockdown_grace_period', $grace);

            $timezone = $user->getPreferenceValue('timesheet.rules.lockdown_period_timezone');
            $this->configurations->offsetSet('timesheet.rules.lockdown_period_timezone', $timezone);
        }
    }
}

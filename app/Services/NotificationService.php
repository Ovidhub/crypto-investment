<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;

class NotificationService
{
    /**
     * Create a new notification
     *
     * @param int $userId
     * @param string $message
     * @param string $title
     * @param string $type
     * @param string|null $link
     * @param string|null $icon
     * @param string|null $iconBgColor
     * @return Notification
     */
    public function createNotification(
        $userId,
        $message, 
        $title, 
        $type = 'info', 
        $link = null, 
        $icon = null, 
        $iconBgColor = null
    ) {
        return Notification::create([
            'user_id' => $userId,
            'message' => $message,
            'title' => $title,
            'type' => $type,
            'link' => $link,
            'icon' => $icon ?? $this->getDefaultIconForType($type),
            'icon_bg_color' => $iconBgColor ?? $this->getDefaultColorForType($type),
            'is_read' => false,
        ]);
    }

    /**
     * Create a deposit notification
     *
     * @param User $user
     * @param float $amount
     * @param string $status
     * @param string|null $link
     * @return Notification
     */
    public function createDepositNotification(User $user, $amount, $status, $link = null)
    {
        $title = 'Deposit ' . ucfirst($status);
        $message = "Your deposit of {$amount} has been {$status}.";
        $icon = 'fa-circle-check';
        $iconBgColor = 'bg-secondary/10';
        
        if ($status === 'pending') {
            $icon = 'fa-clock';
            $iconBgColor = 'bg-primary/10';
        }
        
        return $this->createNotification(
            $user->id,
            $message,
            $title,
            'deposit',
            $link,
            $icon,
            $iconBgColor
        );
    }

    /**
     * Create a withdrawal notification
     *
     * @param User $user
     * @param float $amount
     * @param string $status
     * @param string|null $link
     * @return Notification
     */
    public function createWithdrawalNotification(User $user, $amount, $status, $link = null)
    {
        $title = 'Withdrawal ' . ucfirst($status);
        $message = "Your withdrawal request of {$amount} has been {$status}.";
        $icon = 'fa-circle-check';
        $iconBgColor = 'bg-secondary/10';
        
        if ($status === 'pending') {
            $icon = 'fa-clock';
            $iconBgColor = 'bg-primary/10';
        } elseif ($status === 'rejected') {
            $icon = 'fa-circle-xmark';
            $iconBgColor = 'bg-danger/10';
        }
        
        return $this->createNotification(
            $user->id,
            $message,
            $title,
            'withdrawal',
            $link,
            $icon,
            $iconBgColor
        );
    }

    /**
     * Create an investment notification
     *
     * @param User $user
     * @param string $planName
     * @param float $amount
     * @param string|null $link
     * @return Notification
     */
    public function createInvestmentNotification(User $user, $planName, $amount, $link = null)
    {
        $title = 'New Investment';
        $message = "You have successfully invested {$amount} in {$planName} plan.";
        
        return $this->createNotification(
            $user->id,
            $message,
            $title,
            'investment',
            $link,
            'fa-chart-line',
            'bg-primary/10'
        );
    }

    /**
     * Create an ROI notification
     *
     * @param User $user
     * @param string $planName
     * @param float $amount
     * @param string|null $link
     * @return Notification
     */
     
     
    
     
     
     
    public function createRoiNotification(User $user, $planName, $amount, $link = null)
    {
        $title = 'ROI Received';
        $message = "You have received an ROI of {$amount} from your {$planName} investment.";
        
        return $this->createNotification(
            $user->id,
            $message,
            $title,
            'roi',
            $link,
            'fa-dollar-sign',
            'bg-tertiary/10'
        );
    }

    /**
     * Create a general system notification
     *
     * @param User $user
     * @param string $title
     * @param string $message
     * @param string|null $link
     * @return Notification
     */
    public function createSystemNotification(User $user, $title, $message, $link = null)
    {
        return $this->createNotification(
            $user->id,
            $message,
            $title,
            'system',
            $link,
            'fa-bell',
            'bg-tertiary/10'
        );
    }
    
    /**
     * Get default icon for notification type
     *
     * @param string $type
     * @return string
     */
    private function getDefaultIconForType($type)
    {
        $icons = [
            'info' => 'fa-circle-info',
            'success' => 'fa-circle-check',
            'warning' => 'fa-triangle-exclamation',
            'danger' => 'fa-circle-xmark',
            'deposit' => 'fa-credit-card',
            'withdrawal' => 'fa-money-bill-transfer',
            'investment' => 'fa-chart-line',
            'roi' => 'fa-percent',
            'system' => 'fa-bell',
        ];
        
        return $icons[$type] ?? 'fa-bell';
    }
    
    /**
     * Get default color background for notification type
     *
     * @param string $type
     * @return string
     */
    private function getDefaultColorForType($type)
    {
        $colors = [
            'info' => 'bg-primary/10',
            'success' => 'bg-secondary/10',
            'warning' => 'bg-warning/10',
            'danger' => 'bg-danger/10',
            'deposit' => 'bg-secondary/10',
            'withdrawal' => 'bg-primary/10',
            'investment' => 'bg-primary/10',
            'roi' => 'bg-tertiary/10',
            'system' => 'bg-tertiary/10',
        ];
        
        return $colors[$type] ?? 'bg-primary/10';
    }
} 
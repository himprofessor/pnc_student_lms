<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class UpdateUserProfileImages extends Command
{
    protected $signature = 'users:update-profile-images';
    protected $description = 'Update existing users with sample profile images';

    public function handle()
    {
        $this->info('Updating user profile images...');

        // Get available profile images
        $profileImages = [
            'profile-images/bCWcOcLPrlZIw1zWaEXyCMXBW6jSuJ4hNpudIvwo.jpg',
            'profile-images/E4ymsB2VMGuXLKcFFbWJaNJY8OqINBYjTv1Hptst.jpg',
            'profile-images/faCR2Sy3Wn7WhbEP9bUO3QPz5545vhYG3UHRYnNN.jpg',
            'profile-images/JAVcoQozt56eXYvlQTu9SKpEdjcLoQ0nisiP2OEm.jpg',
            'profile-images/KjOWyycnkAdDNEJjGNPTZ3ykdjU7DAayVDjiNq8h.jpg',
            'profile-images/PyU41jtacGVgmjxStOuv3dzwytNSjp21Ag8x5dUc.jpg',
            'profile-images/QRikEgeYYQ4JIULWDSXKYBx30hEppQZdfVNuT7iJ.jpg',
            'profile-images/WcvVWX7zb2m4Im9hrT8BdNVAXJl3IJIdyR2Z1YPW.jpg',
            'profile-images/wRuOeSySKUMbiBNX8BRA2p4jyIydarhM6mxyIZtg.jpg',
            'profile-images/Y3470OXjxmXeVuYDEB953TgKPj9a1S9n7ErxFudX.jpg',
        ];

        // Get all users without profile images
        $users = User::whereNull('img')->orWhere('img', '')->get();

        if ($users->isEmpty()) {
            $this->info('No users found without profile images.');
            return;
        }

        $imageIndex = 0;
        foreach ($users as $user) {
            $user->update([
                'img' => $profileImages[$imageIndex % count($profileImages)]
            ]);
            
            $this->info("Updated user {$user->name} with profile image: {$profileImages[$imageIndex % count($profileImages)]}");
            $imageIndex++;
        }

        $this->info("Successfully updated {$users->count()} users with profile images!");
    }
}

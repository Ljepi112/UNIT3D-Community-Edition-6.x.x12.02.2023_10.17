<?php
/**
 * NOTICE OF LICENSE.
 *
 * UNIT3D Community Edition is open-sourced software licensed under the GNU Affero General Public License v3.0
 * The details is bundled with this project in the file LICENSE.txt.
 *
 * @project    UNIT3D Community Edition
 *
 * @author     HDVinnie <hdinnovations@protonmail.com>
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

namespace App\Traits;

use App\Models\TwoStepAuth;
use App\Notifications\TwoStepAuthCode;
use Illuminate\Support\Carbon;
use DateTimeInterface;
use Exception;

trait TwoStep
{
    /**
     * Check if the user is authorized.
     *
     * @throws Exception
     */
    private function twoStepVerification(): bool
    {
        $user = auth()->user();
        if ($user !== null) {
            $twoStepAuthStatus = $this->checkTwoStepAuthStatus($user->id);
            if ($twoStepAuthStatus->authStatus !== true) {
                return false;
            }

            return ! $this->checkTimeSinceVerified($twoStepAuthStatus);
        }

        return true;
    }

    /**
     * Check time since user was last verified and take apprpriate action.
     *
     * @throws Exception
     */
    private function checkTimeSinceVerified($twoStepAuth): bool
    {
        $expireMinutes = config('auth.TwoStepVerifiedLifetimeMinutes');
        $now = Carbon::now();
        $expire = Carbon::parse($twoStepAuth->authDate)->addMinutes($expireMinutes);
        $expired = $now->gt($expire);

        if ($expired) {
            $this->resetAuthStatus($twoStepAuth);

            return true;
        }

        return false;
    }

    /**
     * Reset TwoStepAuth collection item and code.
     *
     * @throws Exception
     */
    private function resetAuthStatus($twoStepAuth): mixed
    {
        $twoStepAuth->authCode = $this->generateCode();
        $twoStepAuth->authCount = 0;
        $twoStepAuth->authStatus = 0;
        $twoStepAuth->authDate = null;
        $twoStepAuth->requestDate = null;

        $twoStepAuth->save();

        return $twoStepAuth;
    }

    /**
     * Generate Authorization Code.
     *
     * @throws Exception
     */
    private function generateCode(int $length = 4, string $prefix = '', string $suffix = ''): string
    {
        for ($i = 0; $i < $length; $i++) {
            $prefix .= random_int(0, 1) !== 0 ? \chr(random_int(65, 90)) : random_int(0, 9);
        }

        return $prefix.$suffix;
    }

    /**
     * Create/retreive 2step verification object.
     *
     *
     * @throws Exception
     */
    private function checkTwoStepAuthStatus(int $userId): TwoStepAuth|\Illuminate\Database\Eloquent\Model
    {
        return TwoStepAuth::firstOrCreate(
            [
                'userId' => $userId,
            ],
            [
                'userId'    => $userId,
                'authCode'  => $this->generateCode(),
                'authCount' => 0,
            ]
        );
    }

    /**
     * Retreive the Verification Status.
     */
    protected function getTwoStepAuthStatus(int $userId)
    {
        return TwoStepAuth::where('userId', '=', $userId)->firstOrFail();
    }

    /**
     * Format verification exceeded timings with Carbon.
     */
    protected function exceededTimeParser(DateTimeInterface $time): \Illuminate\Support\Collection
    {
        $tomorrow = Carbon::parse($time)->addMinutes(config('auth.TwoStepExceededCountdownMinutes'))->format('l, F jS Y h:i:sa');
        $remaining = $time->addMinutes(config('auth.TwoStepExceededCountdownMinutes'))->diffForHumans(null, true);

        $data = [
            'tomorrow'  => $tomorrow,
            'remaining' => $remaining,
        ];

        return collect($data);
    }

    /**
     * Check if time since account lock has expired and return true if account verification can be reset.
     */
    protected function checkExceededTime(DateTimeInterface $time): bool
    {
        $now = Carbon::now();
        $expire = Carbon::parse($time)->addMinutes(config('auth.TwoStepExceededCountdownMinutes'));

        return $now->gt($expire);
    }

    /**
     * Method to reset code and count.
     *
     * @throws Exception
     */
    protected function resetExceededTime($twoStepEntry): mixed
    {
        $twoStepEntry->authCount = 0;
        $twoStepEntry->authCode = $this->generateCode();
        $twoStepEntry->save();

        return $twoStepEntry;
    }

    /**
     * Successful activation actions.
     *
     * @throws Exception
     */
    protected function resetActivationCountdown($twoStepAuth): void
    {
        $twoStepAuth->authCode = $this->generateCode();
        $twoStepAuth->authCount = 0;
        $twoStepAuth->authStatus = 1;
        $twoStepAuth->authDate = Carbon::now();
        $twoStepAuth->requestDate = null;

        $twoStepAuth->save();
    }

    /**
     * Send verification code via notify.
     */
    protected function sendVerificationCodeNotification($twoStepAuth, $deliveryMethod = null): void
    {
        $user = auth()->user();
        if ($deliveryMethod === null) {
            $user->notify(new TwoStepAuthCode($user, $twoStepAuth->authCode));
        }

        $twoStepAuth->requestDate = Carbon::now();

        $twoStepAuth->save();
    }
}

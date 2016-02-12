<?php

namespace Clowdy\Raven\Processors;

use Illuminate\Support\Facades\Auth;

/**
 * This processor gets the currently logged in user model via the Auth facade,
 * and adds details about this user to an outgoing error report.
 *
 * The model's `toArray` method is used to get the user data. This is provided
 * by Eloquent and can be overridden to provide extra fields or to remove
 * fields. Fields can also be removed in Eloquent by populating the `$hidden`
 * property.
 *
 * The fields retrieved from `toArray` are then filtered with any options passed
 * to this processor's constructor before being attached to the error report.
 *
 * If no user is logged in, the data `['id' => null]` is still attached to the
 * error object, to differentiate a case where no data on the current user is
 * available from a case where it is known that no user is logged in.
 */
class UserDataProcessor
{
    /**
     * @var array
     */
    protected $options = [
        'except' => [],
        'only' => [
            'id',
        ],
    ];

    /**
     * Make a new UserDataProcessor.
     *
     * Options:
     * - array 'except': fields from the user not to include
     * - array 'only': the only fields from the user to include, or null to
     *                 include all fields (other than those removed by 'except')
     *
     * Note that rather than using these options it may be preferable in certain
     * cases to use the User model's `$hidden` property or overriding its
     * `toArray` method.
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->options = array_merge($this->options, $options);
    }

    /**
     * Run the processor: attach user data to a Monolog record.
     *
     * @param array $record Monolog record
     *
     * @return array $record
     */
    public function __invoke(array $record)
    {
        $data = [];
        if ($user = $this->getUser()) {
            $data = $user->toArray();

            $data = array_except($data, $this->options['except']);

            if (! empty($this->options['only'])) {
                $data = array_only($data, $this->options['only']);
            }
        }

        $record['context']['user'] = array_merge($data, array_get($record, 'context.user', []));

        return $record;
    }

    /**
     * Get user function.
     *
     * @return User
     */
    public function getUser()
    {
        return Auth::user();
    }
}

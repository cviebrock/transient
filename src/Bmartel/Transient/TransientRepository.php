<?php

namespace Bmartel\Transient;

class TransientRepository implements TransientRepositoryInterface
{

    /**
     * Find a Transient by its signature.
     *
     * @param $signature
     *
     * @return null|\Bmartel\Transient\Transient
     */
    public function findBySignature($signature)
    {
        return Transient::where('signature', $signature)->active()->first();
    }

    /**
     * Delete a transient by its signature.
     *
     * @param $signature
     *
     * @return mixed
     */
    public function deleteBySignature($signature)
    {
        return Transient::where('signature', $signature)->delete();
    }

    /**
     * Expire the transient by its signature.
     *
     * @param $signature
     *
     * @return bool|int
     */
    public function expire($signature)
    {
        $transient = $this->findBySignature($signature);

        return ($transient) ? $transient->expire() : false;
    }

    /**
     * @param TransientPropertyInterface $transient
     * @param $signature
     * @param $property
     * @param $value
     * @param $expire
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function store(TransientPropertyInterface $transient, $signature, $property, $value, $expire)
    {
        return $transient->transientProperties()->create(compact('signature', 'property', 'value', 'expire'));
    }

    /**
     * Delete all transient properties.
     *
     * @param bool $expiredOnly
     *
     * @return bool|mixed|null
     */
    public function deleteAll($expiredOnly = true)
    {
        $query = Transient::query();

        if ($expiredOnly) {
            $query->expired();
        }

        return $query->delete();
    }

    /**
     * Delete all properties attached to a given model.
     *
     * @param TransientPropertyInterface $transient
     * @param bool $expiredOnly
     *
     * @return mixed
     */
    public function deleteByModel(TransientPropertyInterface $transient, $expiredOnly = true)
    {
        $query = $transient->transientProperties();

        if ($expiredOnly) {
            $query->expired();
        }

        return $query->delete();
    }

    /**
     * Delete all properties attached to a given model.
     *
     * @param TransientPropertyInterface $transient
     * @param bool $expiredOnly
     *
     * @return mixed
     */
    public function deleteByModelType(TransientPropertyInterface $transient, $expiredOnly = true)
    {
        $query = Transient::where('model_type', get_class($transient));

        if ($expiredOnly) {
            $query->expired();
        }

        return $query->delete();
    }

    /**
     * Delete all properties by given array of property names.
     *
     * @param array $transientProperties
     * @param bool $expiredOnly
     *
     * @return int
     */
    public function deleteByProperty(array $transientProperties, $expiredOnly = true)
    {
        if ($transientProperties) {
            $query = Transient::whereIn('property', $transientProperties);

            if ($expiredOnly) {
                $query->expired();
            }

            return $query->delete();
        }

        // If no arguments are provided, dont delete anything.
        return 0;
    }

    /**
     * Delete all properties listed in the array and attached to the defined model.
     *
     * @param TransientPropertyInterface $transient
     * @param array $transientProperties
     * @param bool $expiredOnly
     *
     * @return int
     */
    public function deleteByModelProperty(
        TransientPropertyInterface $transient,
        array $transientProperties,
        $expiredOnly = true
    ) {
        if ($transientProperties) {

            $query = $transient->transientProperties()->whereIn('property', $transientProperties);

            if ($expiredOnly) {
                $query->expired();
            }

            return $query->delete();
        }

        // If no arguments are provided, dont delete anything.
        return 0;
    }

} 

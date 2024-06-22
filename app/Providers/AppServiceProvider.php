<?php

namespace App\Providers;

use Doctrine\DBAL\Exception;
use Illuminate\Support\ServiceProvider;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     * @throws Exception
     */
    public function boot()
    {
        //
        if (!Type::hasType('bit')) {
            Type::addType('bit', BitType::class);
        }

        $platform = \DB::getDoctrineConnection()->getDatabasePlatform();
        if (!$platform->hasDoctrineTypeMappingFor('bit')) {
            $platform->markDoctrineTypeCommented(Type::getType('bit'));
            $platform->registerDoctrineTypeMapping('bit', 'boolean');
        }
    }
}

class BitType extends Type
{
    const BIT = 'bit';

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return 'BIT(' . ($fieldDeclaration['length'] ?: 1) . ')';
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return (bool) $value;
    }

    public function getName()
    {
        return self::BIT;
    }
}

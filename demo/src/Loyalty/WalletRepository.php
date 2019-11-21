<?php


namespace Loyalty;


use Ramsey\Uuid\UuidInterface;

interface WalletRepository
{
    public function get(UuidInterface $id): Wallet;
    
    public function save(Wallet $wallet): void;
}
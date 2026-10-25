<?php

namespace App\Support;

use App\Models\Orders\MarketplaceOrder;
use Illuminate\Validation\ValidationException;

final class ParcelQrCode
{
    private const PREFIX = 'SARI-PARCEL';

    private const VERSION = '1';

    public function payload(MarketplaceOrder $order): string
    {
        $body = implode('|', [
            self::PREFIX,
            self::VERSION,
            (string) $order->id,
            (string) $order->order_number,
        ]);

        return $body.'|'.$this->signature($body);
    }

    public function resolve(string $payload): MarketplaceOrder
    {
        $parts = explode('|', trim($payload));

        if (count($parts) !== 5) {
            throw $this->invalidCode();
        }

        [$prefix, $version, $orderId, $orderNumber, $signature] = $parts;

        if (
            $prefix !== self::PREFIX
            || $version !== self::VERSION
            || ! ctype_digit($orderId)
            || $orderNumber === ''
            || $signature === ''
        ) {
            throw $this->invalidCode();
        }

        $body = implode('|', [
            $prefix,
            $version,
            $orderId,
            $orderNumber,
        ]);

        if (! hash_equals($this->signature($body), $signature)) {
            throw $this->invalidCode();
        }

        $order = MarketplaceOrder::query()->find((int) $orderId);

        if (! $order || (string) $order->order_number !== $orderNumber) {
            throw $this->invalidCode();
        }

        return $order;
    }

    private function signature(string $body): string
    {
        return hash_hmac('sha256', $body, $this->key());
    }

    private function key(): string
    {
        $key = (string) config('app.key', '');

        if ($key === '') {
            throw ValidationException::withMessages([
                'payload' => 'Parcel QR signing is not configured.',
            ]);
        }

        if (str_starts_with($key, 'base64:')) {
            $decoded = base64_decode(substr($key, 7), true);

            if (is_string($decoded) && $decoded !== '') {
                return $decoded;
            }
        }

        return $key;
    }

    private function invalidCode(): ValidationException
    {
        return ValidationException::withMessages([
            'payload' => 'This QR code is not a valid SARI parcel code.',
        ]);
    }
}

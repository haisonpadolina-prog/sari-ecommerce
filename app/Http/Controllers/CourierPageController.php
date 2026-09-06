<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CourierPageController extends Controller
{
    private function courier(Request $request): array
    {
        return [
            'name' => $request->session()->get('courier_name', 'SARI Courier'),
            'vehicle' => 'Motorcycle',
            'plate_number' => 'NCR 4821',
            'status' => 'Online',
        ];
    }

    private function stats(): array
    {
        return [
            'available_requests' => 12,
            'active_deliveries' => 1,
            'completed_today' => 8,
            'earnings_today' => 1240,
        ];
    }

    private function guard(Request $request)
    {
        if (!$request->session()->get('is_courier')) {
            return redirect()->route('login');
        }

        return null;
    }

    public function requests(Request $request)
    {
        if ($redirect = $this->guard($request)) {
            return $redirect;
        }

        $courier = $this->courier($request);
        $stats = $this->stats();

        $deliveryRequests = [
            [
                'order_number' => 'SARI-1059',
                'seller' => 'Urban Finds PH',
                'pickup' => 'Makati City',
                'buyer' => 'Mika Santos',
                'destination' => 'Pasay City',
                'distance' => '4.8 km',
                'fee' => 85,
                'items' => 2,
                'priority' => 'New',
                'payment' => 'Paid Online',
            ],
            [
                'order_number' => 'SARI-1060',
                'seller' => 'Daily Essentials',
                'pickup' => 'BGC, Taguig',
                'buyer' => 'Paolo Reyes',
                'destination' => 'Mandaluyong City',
                'distance' => '6.2 km',
                'fee' => 110,
                'items' => 1,
                'priority' => 'Nearby',
                'payment' => 'COD',
            ],
            [
                'order_number' => 'SARI-1061',
                'seller' => 'Metro Home Store',
                'pickup' => 'Manila City',
                'buyer' => 'Nicole Tan',
                'destination' => 'Quezon City',
                'distance' => '8.7 km',
                'fee' => 130,
                'items' => 3,
                'priority' => 'New',
                'payment' => 'Paid Online',
            ],
            [
                'order_number' => 'SARI-1062',
                'seller' => 'Tech Corner',
                'pickup' => 'Makati City',
                'buyer' => 'James Cruz',
                'destination' => 'Taguig City',
                'distance' => '5.1 km',
                'fee' => 95,
                'items' => 1,
                'priority' => 'Nearby',
                'payment' => 'Paid Online',
            ],
        ];

        return view('courier.requests', compact(
            'courier',
            'stats',
            'deliveryRequests'
        ));
    }

    public function pickups(Request $request)
    {
        if ($redirect = $this->guard($request)) {
            return $redirect;
        }

        $courier = $this->courier($request);
        $stats = $this->stats();

        $pickups = [
            [
                'order_number' => 'SARI-1058',
                'seller' => 'SARI Seller Store',
                'address' => 'Makati City',
                'contact' => '0917 ••• ••42',
                'buyer' => 'Angela Ramos',
                'destination' => 'Pasay City',
                'scheduled' => 'Today, 8:15 PM',
                'status' => 'Ready for Pickup',
                'items' => 2,
            ],
            [
                'order_number' => 'SARI-1064',
                'seller' => 'Casa Manila Finds',
                'address' => 'Mandaluyong City',
                'contact' => '0995 ••• ••18',
                'buyer' => 'Carla V.',
                'destination' => 'San Juan City',
                'scheduled' => 'Today, 9:00 PM',
                'status' => 'Accepted',
                'items' => 1,
            ],
        ];

        return view('courier.pickups', compact(
            'courier',
            'stats',
            'pickups'
        ));
    }

    public function deliveries(Request $request)
    {
        if ($redirect = $this->guard($request)) {
            return $redirect;
        }

        $courier = $this->courier($request);
        $stats = $this->stats();

        $activeDelivery = [
            'order_number' => 'SARI-1058',
            'seller' => 'SARI Seller Store',
            'pickup' => 'Makati City',
            'buyer' => 'Angela Ramos',
            'destination' => 'Pasay City',
            'distance_left' => '2.4 km',
            'fee' => 120,
            'status' => 'In Transit',
            'progress' => 68,
        ];

        $queue = [
            [
                'order_number' => 'SARI-1064',
                'seller' => 'Casa Manila Finds',
                'destination' => 'San Juan City',
                'status' => 'Waiting for Pickup',
                'fee' => 90,
            ],
        ];

        return view('courier.deliveries', compact(
            'courier',
            'stats',
            'activeDelivery',
            'queue'
        ));
    }

    public function earnings(Request $request)
    {
        if ($redirect = $this->guard($request)) {
            return $redirect;
        }

        $courier = $this->courier($request);
        $stats = $this->stats();

        $earnings = [
            'today' => 1240,
            'week' => 6480,
            'month' => 21850,
            'pending' => 320,
            'available' => 21530,
        ];

        $daily = [
            ['day' => 'Mon', 'amount' => 890],
            ['day' => 'Tue', 'amount' => 1120],
            ['day' => 'Wed', 'amount' => 960],
            ['day' => 'Thu', 'amount' => 1350],
            ['day' => 'Fri', 'amount' => 920],
            ['day' => 'Sat', 'amount' => 1480],
            ['day' => 'Sun', 'amount' => 1240],
        ];

        $transactions = [
            ['order' => 'SARI-1057', 'date' => 'Today · 6:42 PM', 'type' => 'Delivery Fee', 'amount' => 95, 'status' => 'Credited'],
            ['order' => 'SARI-1056', 'date' => 'Today · 5:16 PM', 'type' => 'Delivery Fee', 'amount' => 120, 'status' => 'Credited'],
            ['order' => 'SARI-1055', 'date' => 'Today · 3:48 PM', 'type' => 'Delivery Fee', 'amount' => 80, 'status' => 'Credited'],
            ['order' => 'SARI-1054', 'date' => 'Today · 1:20 PM', 'type' => 'Delivery Fee', 'amount' => 105, 'status' => 'Credited'],
        ];

        return view('courier.earnings', compact(
            'courier',
            'stats',
            'earnings',
            'daily',
            'transactions'
        ));
    }

    public function history(Request $request)
    {
        if ($redirect = $this->guard($request)) {
            return $redirect;
        }

        $courier = $this->courier($request);
        $stats = $this->stats();

        $history = [
            ['order' => 'SARI-1057', 'customer' => 'Angela R.', 'route' => 'Makati → Pasay', 'date' => 'Aug 18, 2026', 'time' => '6:42 PM', 'fee' => 95, 'status' => 'Completed'],
            ['order' => 'SARI-1056', 'customer' => 'Mark D.', 'route' => 'BGC → Mandaluyong', 'date' => 'Aug 18, 2026', 'time' => '5:16 PM', 'fee' => 120, 'status' => 'Completed'],
            ['order' => 'SARI-1055', 'customer' => 'Nicole S.', 'route' => 'Manila → Quezon City', 'date' => 'Aug 18, 2026', 'time' => '3:48 PM', 'fee' => 80, 'status' => 'Completed'],
            ['order' => 'SARI-1054', 'customer' => 'Carla V.', 'route' => 'Makati → Taguig', 'date' => 'Aug 18, 2026', 'time' => '1:20 PM', 'fee' => 105, 'status' => 'Completed'],
            ['order' => 'SARI-1053', 'customer' => 'John P.', 'route' => 'Pasig → San Juan', 'date' => 'Aug 17, 2026', 'time' => '8:05 PM', 'fee' => 90, 'status' => 'Completed'],
            ['order' => 'SARI-1052', 'customer' => 'Mia G.', 'route' => 'Taguig → Pasay', 'date' => 'Aug 17, 2026', 'time' => '6:34 PM', 'fee' => 115, 'status' => 'Completed'],
        ];

        return view('courier.history', compact(
            'courier',
            'stats',
            'history'
        ));
    }

    public function messages(Request $request)
    {
        if ($redirect = $this->guard($request)) {
            return $redirect;
        }

        $courier = $this->courier($request);
        $stats = $this->stats();

        $conversations = [
            [
                'name' => 'Angela Ramos',
                'role' => 'Buyer · SARI-1058',
                'initials' => 'AR',
                'preview' => 'I am near the lobby entrance.',
                'time' => '8:31 PM',
                'unread' => 2,
            ],
            [
                'name' => 'SARI Seller Store',
                'role' => 'Seller · SARI-1058',
                'initials' => 'SS',
                'preview' => 'Package is ready for pickup.',
                'time' => '8:09 PM',
                'unread' => 0,
            ],
            [
                'name' => 'Courier Support',
                'role' => 'SARI Support',
                'initials' => 'CS',
                'preview' => 'Let us know if you need assistance.',
                'time' => '7:40 PM',
                'unread' => 1,
            ],
        ];

        $messages = [
            ['from' => 'them', 'text' => 'Hi! Please message me when you are close to the building.', 'time' => '8:21 PM'],
            ['from' => 'me', 'text' => 'Sure. I am currently on the way to your location.', 'time' => '8:24 PM'],
            ['from' => 'them', 'text' => 'I am near the lobby entrance.', 'time' => '8:31 PM'],
        ];

        return view('courier.messages', compact(
            'courier',
            'stats',
            'conversations',
            'messages'
        ));
    }

    public function profile(Request $request)
    {
        if ($redirect = $this->guard($request)) {
            return $redirect;
        }

        $courier = $this->courier($request);
        $stats = $this->stats();

        $profile = [
            'full_name' => 'SARI Courier',
            'email' => $request->session()->get('courier_email', 'courier@gmail.com'),
            'phone' => '0917 555 4821',
            'address' => 'Makati City, Metro Manila',
            'vehicle_type' => 'Motorcycle',
            'vehicle_model' => 'Honda Click 125i',
            'plate_number' => 'NCR 4821',
            'license_number' => 'N01-26-458721',
            'rating' => '4.94',
            'completed_deliveries' => 184,
            'joined' => 'August 2026',
        ];

        return view('courier.profile', compact(
            'courier',
            'stats',
            'profile'
        ));
    }
}
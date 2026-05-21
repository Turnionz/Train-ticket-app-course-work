<?php

namespace Database\Seeders;

use App\Models\Assignment;
use App\Models\Crew;
use App\Models\Employee;
use App\Models\Route;
use App\Models\RouteStop;
use App\Models\Seat;
use App\Models\Station;
use App\Models\Ticket;
use App\Models\Train;
use App\Models\Trip;
use App\Models\User;
use App\Models\Wagon;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $adminCrew = Crew::factory()->create();
        $adminEmployee = Employee::factory()->for($adminCrew)->create([
            'employee_type' => Employee::$type[0]
        ]);

        User::factory()->for($adminEmployee)->create([
            'email' => 'test@email.com',
            'first_name' => 'admin',
            'last_name' => 'admin',
            'password' => 'password123',
            'role' => User::$role[0]
        ]);

        $trainToWagonMap = [
            'Інтерсіті'    => ['Сидячий'],
            'Регіональний' => ['Сидячий'],
            'Пасажирський' => ['Сидячий', 'Купейний', 'Плацкартний'],
            'Нічний'       => ['Купейний', 'Плацкартний', 'Люкс'],
        ];

        $wagonToSeatMap = [
            'Сидячий'     => ['1-й клас', '2-й клас', '3-й клас'],
            'Купейний'    => ['Спляче'],
            'Плацкартний' => ['3-й клас'],
            'Люкс'        => ['Люкс'],
        ];

        $trainTypes = array_keys($trainToWagonMap);
        $allSeatsToInsert = [];
        $now = now();

        // 1. Fetch the JSON presets exactly ONCE before the loops start!
        $wagonPresets = Wagon::getPresets();

        for ($i = 0; $i <= 55; $i++) {
            $wagonAmount = rand(2, 24);
            $trainType = $trainTypes[array_rand($trainTypes)];

            $train = Train::factory()->create([
                'type' => $trainType
            ]);

            for ($w = 0; $w < $wagonAmount; $w++) {
                $allowedWagons = $trainToWagonMap[$trainType];
                $wagonType = $allowedWagons[array_rand($allowedWagons)];

                $allowedClasses = $wagonToSeatMap[$wagonType];
                $wagonSeatClass = $allowedClasses[array_rand($allowedClasses)];

                // 2. Use the array we fetched from the JSON file
                $layoutMap = $wagonPresets[$wagonType] ?? [];

                $wagon = Wagon::factory()
                    ->for($train)
                    ->create([
                        'wagon_number' => $w + 1,
                        'type' => $wagonType,
                        'layout_map' => $layoutMap
                    ]);

                $seatNumber = 1;

                foreach ($layoutMap as $row) {
                    foreach ($row as $cell) {
                        if ($cell === 'seat') {
                            $allSeatsToInsert[] = [
                                'wagon_id' => $wagon->id,
                                'seat_number' => $seatNumber,
                                'class' => $wagonSeatClass,
                                'created_at' => $now,
                                'updated_at' => $now,
                            ];
                            $seatNumber++;
                        }
                    }
                }
            }
        }

        foreach (array_chunk($allSeatsToInsert, 2000) as $chunk) {
            DB::table('seats')->insert($chunk);
        }

        // Залишаємо створення звичайних користувачів, але без пасажирів
        for ($i = 0; $i <= 126; $i++) {
            User::factory()->create([
                'role' => User::$role[3]
            ]);
        }

        $stations = Station::factory(350)->create();
        $stationIds = $stations->pluck('id')->toArray();

        $connectionsToInsert = [];
        $existingPairs = [];

        foreach ($stationIds as $currentId) {
            $numberOfConnections = rand(1, 3);

            for ($i = 1; $i <= $numberOfConnections; $i++) {
                $targetId = $currentId + $i;

                if (in_array($targetId, $stationIds) && $currentId < $targetId) {
                    $pairKey = $currentId . '-' . $targetId;

                    if (!in_array($pairKey, $existingPairs)) {
                        $existingPairs[] = $pairKey;
                        $connectionsToInsert[] = [
                            'station_a' => $currentId,
                            'station_b' => $targetId,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }
            }
        }

        foreach (array_chunk($connectionsToInsert, 500) as $chunk) {
            DB::table('connected_stations')->insert($chunk);
        }

        for ($r = 1; $r <= 45; $r++) {
            $currentStationId = $stationIds[array_rand($stationIds)];
            $visitedStations = [$currentStationId];

            $route = Route::create([
                'depart_station' => $currentStationId,
                'arrival_station' => $currentStationId
            ]);

            $targetStopsCount = rand(5, 18);
            $actualStops = 1;

            for ($stopOrder = 1; $stopOrder <= $targetStopsCount; $stopOrder++) {
                $isLastStop = ($stopOrder === $targetStopsCount);

                RouteStop::create([
                    'route_id' => $route->id,
                    'station_id' => $currentStationId,
                    'order' => $stopOrder,
                    'stop_time' => $isLastStop ? '00:00:00' : '00:' . str_pad(rand(3, 15), 2, '0', STR_PAD_LEFT) . ':00',
                    'travel_time_to_next_station' => $isLastStop ? '00:00:00' : '0' . rand(0, 2) . ':' . str_pad(rand(10, 59), 2, '0', STR_PAD_LEFT) . ':00',
                ]);

                if ($isLastStop) break;

                $neighbors = $this->getNeighbors($currentStationId);
                $validNextStops = array_diff($neighbors, $visitedStations);

                if (empty($validNextStops)) {
                    break;
                }

                $currentStationId = $validNextStops[array_rand($validNextStops)];
                $visitedStations[] = $currentStationId;
                $actualStops++;
            }

            $route->update([
                'depart_station' => $visitedStations[0],
                'arrival_station' => end($visitedStations)
            ]);
        }

        $trains = Train::all()->shuffle();
        $routes = Route::all()->shuffle();

        for ($i = 0; $i <= 25; $i++) {
            $departTime = Carbon::now()
                ->addDays(rand(0, 30))
                ->setTime(rand(6, 22), rand(0, 59), 0);

            $routeStops = RouteStop::where('route_id', $routes[$i]->id)->get();

            $tripDurationInSeconds = 0;

            foreach ($routeStops as $stop) {
                $tripDurationInSeconds += strtotime($stop->stop_time) - strtotime('TODAY');
                $tripDurationInSeconds += strtotime($stop->travel_time_to_next_station) - strtotime('TODAY');
            }

            $arrivalTime = (clone $departTime)->addSeconds($tripDurationInSeconds);

            $trip = Trip::factory()->create([
                'train_id' => $trains[$i]->id,
                'route_id' => $routes[$i]->id,
                'depart_time' => $departTime,
                'arrival_time' => $arrivalTime,
            ]);

            $crew = Crew::factory()->create();

            Employee::factory(rand(2, 5))
                ->state([
                    'employee_type' => Employee::$type[1],
                    'crew_id' => $crew->id
                ])
                ->has(User::factory()->state(['role' => User::$role[2]]))
                ->create();

            Employee::factory()
                ->state([
                    'employee_type' => Employee::$type[0],
                    'crew_id' => $crew->id
                ])
                ->has(User::factory()->state(['role' => User::$role[2]]))
                ->create();

            Employee::factory(2)
                ->state([
                    'employee_type' => Employee::$type[2],
                    'crew_id' => $crew->id
                ])
                ->has(User::factory()->state(['role' => User::$role[2]]))
                ->create();

            Employee::factory(2)
                ->state([
                    'employee_type' => Employee::$type[3],
                    'crew_id' => $crew->id
                ])
                ->has(User::factory()->state(['role' => User::$role[2]]))
                ->create();

            Assignment::factory()->create([
                'crew_id' => $crew->id,
                'trip_id' => $trip->id
            ]);
        }

        $trips = Trip::with(['train.wagons', 'route.routeStops'])->get();

        // НОВЕ: Отримуємо всіх користувачів зі статусом "пасажир" (User::$role[3]), 
        // щоб пізніше прив'язати їх до квитків.
        $standardUsers = User::where('role', User::$role[3])->get();

        foreach ($trips as $trip) {
            $stops = $trip->route->routeStops->sortBy('order')->values();

            if ($stops->count() < 2) {
                continue;
            }

            $wagonIds = $trip->train->wagons->pluck('id');

            $availableSeats = Seat::whereIn('wagon_id', $wagonIds)
                ->inRandomOrder()
                ->limit(10)
                ->get();

            $ticketsToGenerate = min(10, $availableSeats->count());

            if ($ticketsToGenerate === 0) {
                continue;
            }

            for ($i = 0; $i < $ticketsToGenerate; $i++) {
                $seat = $availableSeats[$i];

                $departIndex = rand(0, $stops->count() - 2);
                $arrivalIndex = rand($departIndex + 1, $stops->count() - 1);

                // НОВЕ: Обираємо випадкового користувача з нашої колекції
                $randomUser = $standardUsers->random();

                Ticket::factory()->create([
                    'trip_id' => $trip->id,
                    'seat_id' => $seat->id,
                    'user_id' => $randomUser->id, // НОВЕ: Прив'язуємо ID користувача
                    'departing_station' => $stops[$departIndex]->station_id,
                    'arriving_station' => $stops[$arrivalIndex]->station_id,
                    'status' => Ticket::$status[1]
                ]);
            }
        }
    }

    private function getNeighbors(int $stationId): array
    {
        $forward = DB::table('connected_stations')
            ->where('station_a', $stationId)
            ->pluck('station_b')
            ->toArray();

        $backward = DB::table('connected_stations')
            ->where('station_b', $stationId)
            ->pluck('station_a')
            ->toArray();

        return array_unique(array_merge($forward, $backward));
    }
}

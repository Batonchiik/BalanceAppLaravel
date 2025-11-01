<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BalanceController extends Controller
{
    public function deposit(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0.01',
            'comment' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated) {
            $user = User::find($validated['user_id']);
            $user->balance += $validated['amount'];
            $user->save();

            Transaction::create([
                'user_id' => $user->id,
                'type' => 'deposit',
                'amount' => $validated['amount'],
                'comment' => $validated['comment'] ?? null,
            ]);
        });

        return response()->json(['message' => 'Успешное пополнение'], 200);
    }

    public function withdraw(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0.01',
            'comment' => 'nullable|string',
        ]);

        $user = User::find($validated['user_id']);

        if ($user->balance < $validated['amount']) {
            return response()->json(['error' => 'Недостаточно средств'], 409);
        }

        DB::transaction(function () use ($user, $validated) {
            $user->balance -= $validated['amount'];
            $user->save();

            Transaction::create([
                'user_id' => $user->id,
                'type' => 'withdraw',
                'amount' => $validated['amount'],
                'comment' => $validated['comment'] ?? null,
            ]);
        });

        return response()->json(['message' => 'Списание успешное'], 200);
    }

    public function transfer(Request $request)
    {
        $validated = $request->validate([
            'from_user_id' => 'required|exists:users,id',
            'to_user_id' => 'required|exists:users,id|different:from_user_id',
            'amount' => 'required|numeric|min:0.01',
            'comment' => 'nullable|string',
        ]);

        $from = User::find($validated['from_user_id']);
        $to = User::find($validated['to_user_id']);

        if ($from->balance < $validated['amount']) {
            return response()->json(['error' => 'Недостаточно средств'], 409);
        }

        DB::transaction(function () use ($from, $to, $validated) {
            $from->balance -= $validated['amount'];
            $to->balance += $validated['amount'];
            $from->save();
            $to->save();

            Transaction::create([
                'user_id' => $from->id,
                'type' => 'transfer_out',
                'amount' => $validated['amount'],
                'comment' => $validated['comment'] ?? null,
            ]);

            Transaction::create([
                'user_id' => $to->id,
                'type' => 'transfer_in',
                'amount' => $validated['amount'],
                'comment' => $validated['comment'] ?? null,
            ]);
        });

        return response()->json(['message' => 'Перевод успешно'], 200);
    }

    public function balance($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'Пользователь не найден'], 404);
        }

        return response()->json([
            'user_id' => $user->id,
            'balance' => $user->balance,
        ]);
    }
}

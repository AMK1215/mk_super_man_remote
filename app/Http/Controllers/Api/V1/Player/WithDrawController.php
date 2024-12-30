<?php

namespace App\Http\Controllers\Api\V1\Player;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\WithdrawRequest;
use App\Http\Resources\Api\V1\WithdrawResource;
use App\Models\DepositRequest;
use App\Models\Webhook\Result;
use App\Models\WithDrawRequest as ModelsWithDrawRequest;
use App\Traits\HttpResponses;
use Illuminate\Support\Facades\Auth;

class WithDrawController extends Controller
{
    use HttpResponses;

    public function withdraw(WithdrawRequest $request)
    {
        $inputs = $request->validated();
        $player = Auth::user();

        if ($player->balanceFloat < $inputs['amount']) {
            return $this->error('', ['amount' => 'Insufficient balance'], 422);
        }
        $depositAmt = DepositRequest::where('user_id', $player->id)->where('status', 1)->sum('amount');
        $result = Result::where('user_id', $player->id)->sum('total_bet_amount');

        if ($depositAmt != $result) {
            return $this->error('', 'Bet Amount is less than your deposit amount', 422);
        }

        $withdraw = ModelsWithDrawRequest::create(array_merge(
            $inputs,
            [
                'payment_type_id' => $request->bank_id,
                'user_id' => $player->id,
                'agent_id' => $player->agent_id,
            ]
        ));

        return $this->success(new WithdrawResource($withdraw), 'Withdraw Request Success');
    }
}

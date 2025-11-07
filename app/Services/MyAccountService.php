<?php
namespace App\Services;

use App\Services\HttpService;

class MyAccountService 
{
    public static function get_credit_account($client_ref)
    {
		$response = HttpService::get(
			API_BASE_URL . "/app001_api_myaccount_balance_v2411001.php", 
			[
				"user_serial" => API_USER_SERIAL,
				"user_activation" => API_USER_ACTIVATION,
				"client_ref" => $client_ref
			]);

		if(isset($response['error'])) {
			return $response;
		} else {
			if(	isset($response['data']) && 
				isset($response['data']['credit_limit']) && 
				$response['data']['credit_limit'] > 0) 
			{
				$spent_percent = (int)(100 * $response['data']['balance'] / $response['data']['credit_limit']);
				$available_percent = 100 - $spent_percent;

                return [
                    'data' => [
                        ...$response['data'],
                        'spent_percent' => $spent_percent,
                        'available_percent' => $available_percent
                    ]
				];
			} else {
                return [];
            }
		}
    }

	public static function send_payment($client_ref, $branch, $amount)
	{
		$response = HttpService::get(
			API_BASE_URL . '/app001_api_echopay_v2411001.php', 
			[
				'user_serial' => API_USER_SERIAL,
				'user_activation' => API_USER_ACTIVATION,
				'client_ref' => $client_ref,
				'payment_type' => 'echopay',
				'branch' => $branch,
				'amount' => $amount,
				'update' => 'N'
			]
		);

		return $response;
	}

	public static function get_last_payment($client_ref)
	{
		$response = HttpService::get(
			API_BASE_URL . '/app001_api_echopay_v2411001.php', 
			[
				'user_serial' => API_USER_SERIAL,
				'user_activation' => API_USER_ACTIVATION,
				'client_ref' => $client_ref,
				'payment_type' => 'echopay',
				'branch' => '0',
				'amount' => 0,
				'update' => 'Y'
			]
		);

		if(	isset($response['data']) && 
			isset($response['data']['payment']) && 
			isset($response['data']['payment']['timeStamp'])) {
			$response['data']['payment']['timeStamp'] = new \DateTime($response['data']['payment']['timeStamp']);
		}

		return $response;
	}

	public static function get_invoice_history($client_ref) 
	{
		$response = HttpService::get(
			API_BASE_URL . '/app001_api_invoice_history_headers_v2412001.php',
			[
				'user_serial' => API_USER_SERIAL,
				'user_activation' => API_USER_ACTIVATION,
				'client_ref' => $client_ref,
			]
		);

		return $response;
	}

	public static function get_invoice_detail($client_ref, $tn, $branch, $dt)
	{
		$response = HttpService::get(
			API_BASE_URL . '/app001_api_invoice_history_details_v2412001.php',
			[
				'user_serial' => API_USER_SERIAL,
				'user_activation' => API_USER_ACTIVATION,
				'client_ref' => $client_ref,
				'tn' => $tn,
				'branch' => $branch,
				'dt' => $dt
			]
		);

		return $response;
	}

	public static function get_loyalty($client_ref)
	{
		$response = HttpService::get(
			API_BASE_URL . '/app001_api_loyalty_session_v2411001.php',
			[
				'user_serial' => API_USER_SERIAL,
				'user_activation' => API_USER_ACTIVATION,
				'client_ref' => $client_ref,
			]
		);

		return $response;
	}

	public static function get_order_history($client_ref)
	{
		$response = HttpService::get(
			API_BASE_URL . '/app001_api_order_history_headers_v2412001.php',
			[
				'user_serial' => API_USER_SERIAL,
				'user_activation' => API_USER_ACTIVATION,
				'client_ref' => $client_ref,
			]
		);

		return $response;		
	}

	public static function get_order_detail($client_ref, $on, $branch, $dt)
	{
		$response = HttpService::get(
			API_BASE_URL . '/app001_api_order_history_details_v2412001.php',
			[
				'user_serial' => API_USER_SERIAL,
				'user_activation' => API_USER_ACTIVATION,
				'client_ref' => $client_ref,
				'on' => $on,
				'branch' => $branch,
				'dt' => $dt
			]
		);

		return $response;
	}

	public static function get_ledger_detail($client_ref)
	{
		$response = HttpService::get(
			API_BASE_URL . '/app001_api_myaccount_ledger_v2411001.php',
			[
				'user_serial' => API_USER_SERIAL,
				'user_activation' => API_USER_ACTIVATION,
				'client_ref' => $client_ref
			]
		);

		return $response;
	}
}
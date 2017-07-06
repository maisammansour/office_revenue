<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Socialite;
use App;

class RevenueController extends Controller
{

	public function calculate(Request $request){

		$data =collect(json_decode ( (file_get_contents ( storage_path ( 'data.json' ) ))));

		$start_date = $request['start_date']."-01";
		$end_date = $request['start_date']."-31";

		// calc revenue = $indefinitely_reserved + $reserved_price
		$revenue = $this->calculate_revenue($data, $start_date, $end_date);
		// calc capacity = $released_capacity+$unreserved_capacity;
		$capacity = $this->calculate_capacity($data, $start_date, $end_date);
		
		return view('revenue', compact('revenue') , compact('capacity'));   

	}

	/**
	 *  calcutae capacity 
	 *  #step1 - offices not reserved in given time
	 *	#step2 - offices that are reserved in given time
	 */
	public function calculate_capacity($data , $start_date , $end_date){


		/**
		 * #step1 office was reserved after given date - start_day is greater than given date
		 */
		$unreserved_capacity = 0;
		// Start_Day > given date
		$unreserved = $data->filter(function ($item)  use($start_date) {
		    return $item->Start_Day > $start_date;
		});
		foreach ($unreserved as $reservation) {
			$unreserved_capacity += $reservation->Capacity;
		}

		/**
		 *  #step2 office was released in given date - end_day is not empty and end_day is less than given date
		 */
		$released_capacity = 0;
		$released = $data->filter(function ($item)  use($start_date) {
		    return $item->End_Day!=""  && $item->End_Day < $start_date ;
		});
		foreach ($released as $reservation) {
			$released_capacity += $reservation->Capacity;
		}
		// calc final capacity
		$capacity = $released_capacity+$unreserved_capacity;
		return $capacity;

	}

	/**
	 *  calcutae revenue 
	 *  #step1 - offices that are indefinitely reserved
	 *	#step2 - offices that are reserved in given time
	 */
	public function calculate_revenue($data , $start_date , $end_date){

		/**
		 * #step1 $indefinitely_reserved = End_Day is empty and Start_Day is reserved before given date - add monthly price
		 */
		$indefinitely_reserved_price= 0;
		// empty end day
		$indefinitely_reserved = $data->where('End_Day', "");
		$indefinitely_reserved->all();
		// Start_Day < given date
		$indefinitely_reserved = $indefinitely_reserved->filter(function ($item)  use($start_date) {
		    return $item->Start_Day < $start_date;
		});
		foreach ($indefinitely_reserved as $reservation) {
			$indefinitely_reserved_price += $reservation->Monthly_Price;
		}

		/**
		 * #step2 $reserved = End_Day is not empty and End_Day month is reserved in given date
		 */
		$reserved_price= 0;
		$reserved = $data->filter(function ($item)  use($start_date , $end_date) {
		    return $item->End_Day >= $start_date && $item->End_Day <= $end_date;
		});
		foreach ($reserved as $reservation) {
			// calculate days between two dates
			$date1=date_create($reservation->Start_Day);
			$date2=date_create($reservation->End_Day);
			$diff=date_diff($date1,$date2);
			$days =  $diff->format("%a");
			// final price is : ((End_Day- Start_Day) / 30 ) * Monthly_Price
			$reserved_price += (($days) / 30) * $reservation->Monthly_Price; 
		}
		// final revenue
		$revenue = $indefinitely_reserved_price + $reserved_price;
		return $revenue;
	}
}
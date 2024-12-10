<?php

namespace App\Exports\Reports;

use App\Models\CustomerComplaint;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ComplaintRegisterExcel implements FromCollection, WithHeadings, WithCustomStartCell
{

	public function collection()
	{
		if (request()->client_cd != '') {
			$query = CustomerComplaint::where('CUST_CD', request()->client_cd);
		} else {
			$query = CustomerComplaint::query();
		}
		if (request()->date_from != '' && request()->date_to != '') {
			$query->whereBetween('COMPL_DT', [request()->date_from, request()->date_to]);
		}
		$data = $query->orderBy('COMPL_DT', 'DESC')->get();
		$datas = [];

		// Define the lookup arrays
		$COMPL_TYPE = [
			'DB_Object' => 'DB Object',
			'Form' => 'Form',
			'Graph' => 'Graph',
			'Others' => 'Others',
			'Report' => 'Report',
			'Tables' => 'Tables',
			'Views' => 'Views',
		];
		$ERROR_TYPE = [
			'DP' => 'Database Problem',
			'NR' => 'New Requirement',
			'OT' => 'Others',
			'SP' => 'Software Problem',
			'ST' => 'Support',
			'UP' => 'User Problem',
		];
		$COMPL_LEVEL = [
			'C' => 'Critical',
			'L' => 'Low',
			'M' => 'Medium',
		];
		$STATUS = [
			'CL' => 'Cancel',
			'CM' => 'Complete',
			'HL' => 'Hold',
			'PN' => 'Pending',
			'SV' => 'Sent For Customer Verification',
		];

		foreach ($data as $dt) {
			$x = new \stdClass();
			// Query for MODULE_NAME
			$moduleResult = DB::select(
				"SELECT MODULE_TEXT FROM SAP_MODULE_DTL WHERE DEPT_MODULE='TERMS' AND MODULE_TEXT = ?",
				[$dt['module']],
			);
			$module = !empty($moduleResult) ? $moduleResult[0]->module_text : '-';

			$cust_cd = $dt['cust_cd'];
			$customer_name =  DB::select("SELECT CLIENT_NAME FROM CELINT_MASTER WHERE ERP_VERT='TERMS' AND CLIENT_CD = '$cust_cd' ORDER BY 1");
			$x->complaint_no = $dt['complaint_no'];
			$x->cust_name = @$customer_name[0]->client_name;
			$x->status =  $STATUS[$dt['status']];
			$x->compl_dt = !empty($data->compl_dt) ? date('d-m-Y', strtotime($data->compl_dt)) : '-';
			$x->module = $module;
			$x->compl_type = $COMPL_TYPE[$dt['compl_type']];
			$x->error_type =  $ERROR_TYPE[$dt['error_type']];
			$x->problem_desc = $dt['problem_desc'];
			$x->reason = $dt['reason'];
			$x->action = $dt['action'];
			$x->close_dt = !empty($data->close_dt) ? date('d-m-Y', strtotime($data->close_dt)) : '-';
			$x->user_name = $dt['user_name'];
			array_push($datas, $x);
		}

		$collection = Collection::make($datas);
		return $collection;
	}

	public function headings(): array
	{
		return [
			['Customer', request()->client_cd],
			['From', request()->date_from],
			['To', request()->date_to],
			[''],
			[
				'Complaint No', 'Compl Dt',  'Cust name', 'Status', 'Module', 'Compl Type', 'Error Type',
				'Problem Desc',  'Reason', 'Action', 'Close Dt', 'User Name',
			]
		];
	}


	public function startCell(): string
	{
		return 'A2';
	}
}

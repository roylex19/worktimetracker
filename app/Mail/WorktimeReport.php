<?php

namespace App\Mail;

use App\Exports\GeneralReportsExport;
use App\Exports\RecordsExport;
use App\Filters\RecordsFilter;
use App\Http\Controllers\Api\RecordsController;
use App\Record;
use App\User;
use DateTime;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class WorktimeReport extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $fileName = 'temp.xlsx';
/*
        $date = (date('H') == 0 && date('i') == 0) ? (new DateTime(date('Y-m-d')))->modify('-1 day')->format('Y-m-d') : date('Y-m-d');

        $records = Record::
              join('projects', 'projects.id', '=', 'records.project_id')
            ->join('users', 'users.id', '=', 'records.user_id')
            ->join('departments', 'departments.id', '=', 'users.department_id')
            ->select('date', 'projects.title as project', 'users.name as name', 'hours', 'description', 'departments.title as department')
            ->whereDate('records.created_at', $date)
            ->orderBy('date')
            ->orderBy('name')
            ->orderBy('records.created_at')
            ->get()
            ->toArray();
*/
        $reports = [];

        $month = date('m');
        $year = date('Y');

        $date = date("Y-m-d", mktime(0, 0, 0, $month, 1, $year));

        $users = User::with('department')->where('disabled', false)->get()->toArray();

        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        for ($j = 0; $j < $daysInMonth; $j++) {

            $oldDate = $date;

            foreach ($users as $user) {
                $records = Record::with('user', 'project', 'department')
                    ->where('date',  $oldDate)
                    ->where('user_id',  $user['id'])
                    ->latest()->get()->toArray();

                $newDate = date('d.m.Y', strtotime($oldDate));

                if (!empty($records)) {
                    foreach ($records as $record) {
                        $desc = preg_replace('/[\r\n]/', ' ', $record['description']);
                        $reports[] = [
                            'date' => $newDate,
                            'name' => $record['user']['name'],
                            'project' => $record['project']['title'],
                            'description' => $desc,
                            'hours' => (string)$record['hours'],
                            'department' => $user['department'] != null ? $user['department']['title'] : '',
                        ];
                    }
                } else {
                    $reports[] = [
                        'date' => $newDate,
                        'name' => $user['name'],
                        'project' => '',
                        'description' => '',
                        'hours' => 0,
                        'department' => $user['department'] != null ? $user['department']['title'] : '',
                    ];
                }
            }
            $date = date("Y-m-d", strtotime($oldDate . '+ 1 days'));
        }

        Excel::store(new GeneralReportsExport($reports), $fileName);

        return $this->view('emails.report')
                    ->attachData(Storage::get($fileName), 'Отчет о проделанной работе '. date('d.m.Y') .'.xlsx');
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\Log;
use Illuminate\Console\Command;

use App\Models\Category;
use App\Models\Course;
use App\Models\Group;
use App\Models\Quiz;

use App\Models\Offset;
use App\Models\Question;
use App\Models\Analytic;
use App\Models\ProctoringResult;

class ClearData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sinau:clear {table} {password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Truncate table if error happen';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        //Start Function Clear
        if ($this->argument('password') == 'alex12345') 
        {
            if ($this->argument('table') == 'category') 
            {
                Category::truncate();
                Offset::where('item', 'category')->update(['offset' => 0]);

                $this->info('Finish Truncate Table: '.$this->argument('table'));
            }
            elseif ($this->argument('table') == 'course') 
            {
                Course::truncate();
                Offset::where('item', 'course')->update(['offset' => 0]);

                $this->info('Finish Truncate Table: '.$this->argument('table'));
            }
            elseif ($this->argument('table') == 'group') 
            {
                Group::truncate();
                $this->info('Finish Truncate Table: '.$this->argument('table'));
            }
            elseif ($this->argument('table') == 'quiz') 
            {
                Quiz::truncate();
                $this->info('Finish Truncate Table: '.$this->argument('table'));
            }
            elseif ($this->argument('table') == 'question') 
            {
                Question::truncate();
                $this->info('Finish Truncate Table: '.$this->argument('table'));
            }
            elseif ($this->argument('table') == 'analytic') 
            {
                Analytic::truncate();
                $this->info('Finish Truncate Table: '.$this->argument('table'));
            }
            elseif ($this->argument('table') == 'proctoring') 
            {
                ProctoringResult::truncate();
                $this->info('Finish Truncate Table: '.$this->argument('table'));
            }
            else
            {
                $this->info('Please choose table first...');
            }
        }
        else
        {
            $this->info('Wrong Password');
        }
        //End Function Clear
    }
}

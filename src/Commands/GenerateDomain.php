<?php

namespace oleglfed\DataModel\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Finder\Finder;

class GenerateDomain extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:doc
                        {--path= : eloquent path}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Data Model Documentation';


    /**
     * GenerateDomain constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->createDirectories();
        $eloquentPath = $this->option('path');
        $templatePath = __DIR__ . '/../../resources/template.html';
        $parsedTemplate = file_get_contents($templatePath);

        if (!is_dir(base_path($eloquentPath))) {
            $this->warn("Directory " . base_path($eloquentPath) . " is not exists");
            exit();
        }

        $files = Finder::create()->in(base_path($eloquentPath))->files()->name('/\Eloquent.php$/');

        if (!$files->count()) {
            $this->warn("Directory " . base_path($eloquentPath) . " does not contain any eloquent files");
            exit();
        }

        $eloquents = [];
        foreach ($files as $file) {
            $parsedFile = file_get_contents($file->getPathname());
            preg_match('/namespace (.{0,});/', $parsedFile, $namespace);

            $eloquents[] = $namespace[1] . '\\' . substr($file->getFilename(), 0, -4);
        }

        file_put_contents(base_path('public/models/index.html'), $this->prepare($parsedTemplate, $eloquents));
        $this->info("Done");
    }

    /**
     * Prepare directories.
     *
     * @return bool
     */
    public function createDirectories()
    {
        if (!file_exists(base_path('public/models'))) {
            mkdir(base_path('public/models'), 0777);
        }

        return true;
    }

    /**
     * @param $fileContent
     * @param $classes
     * @return mixed
     */
    public function prepare($fileContent, $classes)
    {
        $replacings = [
            '{model}',
        ];

        $html = null;
        foreach ($classes as $class) {
            $html .= $this->getDataModel($class);
        }

        $replacements = [
            $html
        ];

        return str_replace($replacings, $replacements, $fileContent);
    }

    /**
     * @return null|string
     */
    public function getDataModel($class)
    {
        //$class::getTable();
        $dbFields = $this->parseDbTable($class::TABLE_NAME);
        view()->addLocation(__DIR__ . '/../../resources');
        return view('table', ['fields' => $dbFields, 'title' => $class])->render();
    }

    /**
     * @param $table
     * @return \Illuminate\Support\Collection
     * @throws \Exception
     */
    public function parseDbTable($table)
    {
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable($table)) {
                return DB::select('show columns from ' . $table);
            }
            return collect([]);
        } catch (\Exception $e) {
            throw new \Exception('Database is not connected');
        }
    }
}

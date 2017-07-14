<?php

namespace Rogue\Console\Commands;

use Rogue\Models\Post;
use Rogue\Services\AWS;
use Illuminate\Console\Command;
use Intervention\Image\Facades\Image;

class EditImagesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rogue:images {start=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create cropped & resized images for every post.';

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
     * @return mixed
     */
    public function handle(AWS $aws)
    {
        $start = $this->argument('start');

        Post::where('id', '>', $start)->chunk(100, function ($posts) use ($aws) {
            foreach ($posts as $post) {
                $image = $post->url;
                $editedImage = (string) Image::make($image)
                    ->orientate()
                    ->fit(400)
                    ->encode('jpg', 75);

                $aws->storeImageData($editedImage, 'edited_'.$post->id);

                $this->line('Saved edited image for post '.$post->id);
            }
        });
    }
}

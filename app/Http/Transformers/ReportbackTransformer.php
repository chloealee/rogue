<?php

namespace Rogue\Http\Transformers;

use Rogue\Models\Post;
use League\Fractal\TransformerAbstract;

class ReportbackTransformer extends TransformerAbstract
{
    /**
     * Transform resource data.
     *
     * @param \Rogue\Models\Post $post
     * @return array
     */
    public function transform(Post $post)
    {
        $signup = $post->signup;

        $result = [
            'id' => $post->id,
            'status' => $post->status,
            'caption' => $post->caption,
            'event_id' => $post->events[0]->id,
            // Add link to review reportback item in Rogue here once that page exists
            // 'uri' => 'link_goes_here'
            'media' => [
                'uri' => $post->url,
                'type' => 'image',
            ],
            'created_at' => $post->created_at->toIso8601String(),
            'reportback' => [
                'id' => $signup->id,
                'created_at' => $signup->created_at->toIso8601String(),
                'updated_at' => $signup->updated_at->toIso8601String(),
                'quantity' => $signup->quantity,
                'why_participated' => $signup->why_participated,
                'flagged' => 'false',
            ],
            'kudos' => [
                    'total' => $post->reactions_count,
                    'data' => [
                        'current_user' => [
                            'kudos_id' => 1,
                            'reacted' => count($post->reactions) >= 1,
                        ],
                        'term' => [
                            'name' => 'heart',
                            'id' => 641,
                            'total' => $post->reactions_count,
                        ],
                    ],
            ],
            'user' => $signup->northstar_id,
        ];

        return $result;
    }
}

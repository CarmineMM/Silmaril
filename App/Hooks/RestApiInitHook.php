<?php

namespace Silmaril\App\Hooks;

class RestApiInitHook
{
    /**
     * Agregar el feature_media a las respuesta rest
     * 
     * @return void
     */
    public static function getFeatureMedia(): void
    {
        \register_rest_field(
            object_type: ['post'],
            attribute: 'featured_images',
            args: [
                'get_callback' => function (array $post): mixed {
                    if ($post['featured_media'] === 0) {
                        return false;
                    }

                    $images = [];

                    foreach (\get_intermediate_image_sizes() as $size) {
                        $image = \wp_get_attachment_image_src($post['featured_media'], $size);

                        $images[$image[3] === false ? 'full' : $size] = [
                            'url' => $image[0],
                            'width' => $image[1],
                            'height' => $image[2],
                            'resized' => $image[3],
                        ];

                        if ($image[3] === false) {
                            break;
                        }
                    }

                    return $images;
                }
            ]
        );
    }
}

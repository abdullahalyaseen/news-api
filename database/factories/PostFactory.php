<?php

namespace Database\Factories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    /**
     * @var string
     */
    protected $model = Post::class;

    /**
     * @return array
     */
    public function definition()
    {
        return [
            'title'=>$this->faker->text(35) ,
            'datetime'=>$this->faker->dateTime ,
            'content'=>$this->faker->text(400) ,
            'user_id'=>$this->faker->numberBetween(1,50) ,
            'category_id'=>$this->faker->numberBetween(1,15) ,
            'featured_image'=>$this->faker->imageUrl() ,
            'vote_up'=>$this->faker->numberBetween(1,100) ,
            'vote_down'=>$this->faker->numberBetween(1,100),
            'vote_up_ids'=>array(),
            'vote_down_ids'=>array(),
        ];
    }
}

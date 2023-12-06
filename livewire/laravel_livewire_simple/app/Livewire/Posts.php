<?php

namespace App\Livewire;

use Illuminate\Support\Str;
use Livewire\Component;
use App\Models\Post;
use App\Models\Kategori;

class Posts extends Component
{

    public $title, $content, $postId, $slug, $status, $updatePost = false, $addPost = false, $kategori_id;
    
    protected $rules = [
        'title' => 'required',
        'content' => 'required',
        'status' => 'required',
        'kategori_id' => 'required',
    ];

    /**
     * Reseting all inputted fields
     * @return void
     */
    public function resetFields()
    {
        $this->title = '';
        $this->content = '';
        $this->kategori_id = '';
        $this->status = 1;
    }

    /**
     * render the post data
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    

    public function render()
    {
        $posts = Post::all();
        $kategori = Kategori::all();
        $data = ['posts' => $posts, 'kategori' => $kategori];
        // json_encode($kategori);
        // var_dump($kategori);
        // die();
        return view('livewire.post', $data);
    }

    // public function show(){
    //     $project = Project::all();
    //     $kategori = Kategori::all();
    //     $data = ['project' => $project, 'kategori' => $kategori];
    //     return view('admin.project.project_data', $data);
    //     // echo json_encode($data);
    // }

    /**
     * Open Add Post form
     * @return void
     */
    public function create()
    {
        $this->resetFields();
        $this->addPost = true;
        $this->updatePost = false;
    }

    /**
     * store the user inputted post data in the posts table
     * @return void
     */
    public function store()
    {
        $this->validate();
        try {
            Post::create([
                'title' => $this->title,
                'content' => $this->content,
                'status' => $this->status,
                'slug' => Str::slug($this->title),
                'kategori_id' => $this->kategori_id,
            ]);

            session()->flash('success', 'Post Created Successfully!!');
            $this->resetFields();
            $this->addPost = false;
        } catch (\Exception $ex) {
            session()->flash('error', 'Something goes wrong!!');
        }
    }

    /**
     * show existing post data in edit post form
     * @param mixed $id
     * @return void
     */
    public function edit($id)
    {
        try {
            $post = Post::findOrFail($id);
            if (!$post) {
                session()->flash('error', 'Post not found');
            } else {
                $this->title = $post->title;
                $this->content = $post->content;
                $this->kategori_id = $post->kategori_id;
                $this->status = $post->status;
                $this->postId = $post->id;
                $this->updatePost = true;
                $this->addPost = false;
            }
        } catch (\Exception $ex) {
            session()->flash('error', 'Something goes wrong!!');
        }

    }

    /**
     * update the post data
     * @return void
     */
    public function update()
    {
        $this->validate();
        try {
            Post::whereId($this->postId)->update([
                'title' => $this->title,
                'content' => $this->content,
                'kategori_id' => $this->kategori_id,
                'status' => $this->status,
                'slug' => Str::slug($this->title)
            ]);
            session()->flash('success', 'Post Updated Successfully!!');
            $this->resetFields();
            $this->updatePost = false;
        } catch (\Exception $ex) {
            session()->flash('error', 'Something goes wrong!!');
        }
    }

    /**
     * Cancel Add/Edit form and redirect to post listing page
     * @return void
     */
    public function cancel()
    {
        $this->addPost = false;
        $this->updatePost = false;
        $this->resetFields();
    }

    /**
     * delete specific post data from the posts table
     * @param mixed $id
     * @return void
     */
    public function destroy($id)
    {
        try {
            Post::find($id)->delete();
            session()->flash('success', "Post Deleted Successfully!!");
        } catch (\Exception $e) {
            session()->flash('error', "Something goes wrong!!");
        }
    }
}

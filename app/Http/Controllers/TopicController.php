<?php
/**
 * NOTICE OF LICENSE.
 *
 * UNIT3D Community Edition is open-sourced software licensed under the GNU Affero General Public License v3.0
 * The details is bundled with this project in the file LICENSE.txt.
 *
 * @project    UNIT3D Community Edition
 *
 * @author     HDVinnie <hdinnovations@protonmail.com>
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

namespace App\Http\Controllers;

use App\Achievements\UserMade100Posts;
use App\Achievements\UserMade200Posts;
use App\Achievements\UserMade25Posts;
use App\Achievements\UserMade300Posts;
use App\Achievements\UserMade400Posts;
use App\Achievements\UserMade500Posts;
use App\Achievements\UserMade50Posts;
use App\Achievements\UserMade600Posts;
use App\Achievements\UserMade700Posts;
use App\Achievements\UserMade800Posts;
use App\Achievements\UserMade900Posts;
use App\Achievements\UserMadeFirstPost;
use App\Models\Forum;
use App\Models\Post;
use App\Models\Topic;
use App\Repositories\ChatRepository;
use Illuminate\Http\Request;
use Exception;

/**
 * @see \Tests\Todo\Feature\Http\Controllers\TopicControllerTest
 */
class TopicController extends Controller
{
    /**
     * TopicController Constructor.
     */
    public function __construct(private readonly ChatRepository $chatRepository)
    {
    }

    /**
     * Show The Topic.
     */
    public function topic(int $id): \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\Illuminate\Http\RedirectResponse
    {
        // Find the topic
        $topic = Topic::findOrFail($id);

        // Get the forum of the topic
        $forum = $topic->forum;

        // The user can post a topic here ?
        if (! $forum->getPermission()->read_topic) {
            // Redirect him to the forum index
            return to_route('forums.index')
                ->withErrors('You Do Not Have Access To Read This Topic!');
        }

        // Increment view
        $topic->views++;
        $topic->save();

        return view('forum.topic.show', [
            'topic' => $topic,
            'forum' => $forum,
        ]);
    }

    /**
     * Topic Add Form.
     */
    public function addForm(Request $request, int $id): \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\Illuminate\Http\RedirectResponse
    {
        $forum = Forum::findOrFail($id);
        $category = Forum::findOrFail($id);

        // The user has the right to create a topic here?
        if (! $category->getPermission()->start_topic) {
            return to_route('forums.index')
                ->withErrors('You Cannot Start A New Topic Here!');
        }

        return view('forum.forum_topic.create', [
            'forum'    => $forum,
            'category' => $category,
            'title'    => $request->input('title'),
        ]);
    }

    /**
     * Create A New Topic In The Forum.
     */
    public function newTopic(Request $request, int $id): \Illuminate\Http\RedirectResponse
    {
        $user = $request->user();
        $forum = Forum::findOrFail($id);
        $category = Forum::findOrFail($id);

        // The user has the right to create a topic here?
        if (! $category->getPermission()->start_topic) {
            return to_route('forums.index')
                ->withErrors('You Cannot Start A New Topic Here!');
        }

        // Create The Topic
        $topic = new Topic();
        $topic->name = $request->input('title');
        $topic->state = 'open';
        $topic->first_post_user_id = $user->id;
        $topic->last_post_user_id = $user->id;
        $topic->first_post_user_username = $user->username;
        $topic->last_post_user_username = $user->username;
        $topic->views = 0;
        $topic->pinned = false;
        $topic->forum_id = $forum->id;

        $v = validator($topic->toArray(), [
            'name'                     => 'required',
            'state'                    => 'required',
            'num_post'                 => '',
            'first_post_user_id'       => 'required',
            'first_post_user_username' => 'required',
            'last_post_user_id'        => '',
            'last_post_user_username'  => '',
            'views'                    => '',
            'pinned'                   => '',
            'forum_id'                 => 'required',
        ]);

        if ($v->fails()) {
            return to_route('forums.index')
                ->withErrors($v->errors());
        }

        $topic->save();
        $post = new Post();
        $post->content = $request->input('content');
        $post->user_id = $user->id;
        $post->topic_id = $topic->id;
        $v = validator($post->toArray(), [
            'content'  => 'required',
            'user_id'  => 'required',
            'topic_id' => 'required',
        ]);
        if ($v->fails()) {
            return to_route('forums.index')
                ->withErrors($v->errors());
        }

        $post->save();
        $topic->num_post = 1;
        $topic->last_reply_at = $post->created_at;
        $topic->save();
        $forum->num_topic = $forum->getTopicCount($forum->id);
        $forum->num_post = $forum->getPostCount($forum->id);
        $forum->last_topic_id = $topic->id;
        $forum->last_topic_name = $topic->name;
        $forum->last_post_user_id = $user->id;
        $forum->last_post_user_username = $user->username;
        $forum->save();

        // Post To ShoutBox
        $appurl = config('app.url');
        $topicUrl = sprintf('%s/forums/topics/%s', $appurl, $topic->id);
        $profileUrl = sprintf('%s/users/%s', $appurl, $user->username);

        if (config('other.staff-forum-notify') && ($forum->id == config('other.staff-forum-id') || $forum->parent_id == config('other.staff-forum-id'))) {
            $forum->notifyStaffers($user, $topic);
        } else {
            $this->chatRepository->systemMessage(sprintf('[url=%s]%s[/url] has created a new topic [url=%s]%s[/url]', $profileUrl, $user->username, $topicUrl, $topic->name));
            $forum->notifySubscribers($user, $topic);
        }

        //Achievements
        $user->unlock(new UserMadeFirstPost(), 1);
        $user->addProgress(new UserMade25Posts(), 1);
        $user->addProgress(new UserMade50Posts(), 1);
        $user->addProgress(new UserMade100Posts(), 1);
        $user->addProgress(new UserMade200Posts(), 1);
        $user->addProgress(new UserMade300Posts(), 1);
        $user->addProgress(new UserMade400Posts(), 1);
        $user->addProgress(new UserMade500Posts(), 1);
        $user->addProgress(new UserMade600Posts(), 1);
        $user->addProgress(new UserMade700Posts(), 1);
        $user->addProgress(new UserMade800Posts(), 1);
        $user->addProgress(new UserMade900Posts(), 1);

        return to_route('forum_topic', ['id' => $topic->id])
            ->withSuccess('Topic Created Successfully!');
    }

    /**
     * Topic Edit Form.
     */
    public function editForm(int $id): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        $topic = Topic::findOrFail($id);
        $categories = Forum::where('parent_id', '!=', 0)->get();

        return view('forum.topic.edit', ['topic' => $topic, 'categories' => $categories]);
    }

    /**
     * Edit Topic In The Forum.
     */
    public function editTopic(Request $request, int $id): \Illuminate\Http\RedirectResponse
    {
        $user = $request->user();
        $topic = Topic::findOrFail($id);

        abort_unless($user->group->is_modo || $user->id === $topic->first_post_user_id, 403);
        $name = $request->input('name');
        $forumId = $request->input('forum_id');
        $topic->name = $name;
        $topic->forum_id = $forumId;
        $topic->save();

        return to_route('forum_topic', ['id' => $topic->id])
            ->withSuccess('Topic Successfully Edited');
    }

    /**
     * Close The Topic.
     */
    public function closeTopic(Request $request, int $id): \Illuminate\Http\RedirectResponse
    {
        $user = $request->user();
        $topic = Topic::findOrFail($id);

        abort_unless($user->group->is_modo || $user->id === $topic->first_post_user_id, 403);
        $topic->state = 'close';
        $topic->save();

        return to_route('forum_topic', ['id' => $topic->id])
            ->withSuccess('This Topic Is Now Closed!');
    }

    /**
     * Open The Topic.
     */
    public function openTopic(Request $request, int $id): \Illuminate\Http\RedirectResponse
    {
        $user = $request->user();
        $topic = Topic::findOrFail($id);

        abort_unless($user->group->is_modo || $user->id === $topic->first_post_user_id, 403);
        $topic->state = 'open';
        $topic->save();

        return to_route('forum_topic', ['id' => $topic->id])
            ->withSuccess('This Topic Is Now Open!');
    }

    /**
     * Delete The Topic and The Posts.
     *
     * @throws Exception
     */
    public function deleteTopic(Request $request, int $id): \Illuminate\Http\RedirectResponse
    {
        $user = $request->user();
        $topic = Topic::findOrFail($id);

        abort_unless($user->group->is_modo || ($user->id === $topic->first_post_user_id && $topic->num_post <= 1 && $topic->created_at->greaterThan(now()->subDay())), 403);
        $posts = $topic->posts();
        $posts->delete();
        $topic->delete();

        return to_route('forums.show', ['id' => $topic->forum->id])
            ->withSuccess('This Topic Is Now Deleted!');
    }

    /**
     * Pin The Topic.
     */
    public function pinTopic(int $id): \Illuminate\Http\RedirectResponse
    {
        $topic = Topic::findOrFail($id);
        $topic->pinned = 1;
        $topic->save();

        return to_route('forum_topic', ['id' => $topic->id])
            ->withSuccess('This Topic Is Now Pinned!');
    }

    /**
     * Unpin The Topic.
     */
    public function unpinTopic(int $id): \Illuminate\Http\RedirectResponse
    {
        $topic = Topic::findOrFail($id);
        $topic->pinned = 0;
        $topic->save();

        return to_route('forum_topic', ['id' => $topic->id])
            ->withSuccess('This Topic Is Now Unpinned!');
    }
}

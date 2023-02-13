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

use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Models\TicketCategory;
use App\Models\TicketPriority;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    final public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        return view('ticket.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    final public function create(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        $categories = TicketCategory::all()->sortBy('position');
        $priorities = TicketPriority::all()->sortBy('position');

        return view('ticket.create', [
            'categories' => $categories,
            'priorities' => $priorities,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    final public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $user = $request->user();

        $ticket = new Ticket();
        $ticket->user_id = $user->id;
        $ticket->category_id = $request->input('category');
        $ticket->priority_id = $request->input('priority');
        $ticket->subject = $request->input('subject');
        $ticket->body = $request->input('body');

        $v = validator($ticket->toArray(), [
            'user_id'     => 'required|exists:users,id',
            'category_id' => 'required|exists:ticket_categories,id',
            'priority_id' => 'required|exists:ticket_priorities,id',
            'subject'     => 'required',
            'body'        => 'required',
        ]);

        if ($v->fails()) {
            return to_route('tickets.create')
                ->withInput()
                ->withErrors($v->errors());
        }

        $ticket->save();

        return to_route('tickets.show', ['id' => $ticket->id])
            ->withSuccess(trans('ticket.created-success'));
    }

    /**
     * Display the specified resource.
     */
    final public function show(Request $request, int $id): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        $user = $request->user();
        $ticket = Ticket::with(['comments'])->findOrFail($id);
        abort_unless($user->group->is_modo || $user->id == $ticket->user_id, 403);

        if ($user->id == $ticket->user_id) {
            $ticket->user_read = 1;
            $ticket->save();
        }

        if ($user->id == $ticket->staff_id) {
            $ticket->staff_read = 1;
            $ticket->save();
        }

        return view('ticket.show', [
            'user'   => $user,
            'ticket' => $ticket,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    final public function update(Request $request, int $id): \Illuminate\Http\RedirectResponse
    {
        $ticket = Ticket::findOrFail($id);
        $user = $request->user();
        abort_unless($user->group->is_modo || $user->id == $ticket->user_id, 403);

        $ticket->category_id = $request->input('category');
        $ticket->priority_id = $request->input('priority');
        $ticket->subject = $request->input('subject');
        $ticket->body = $request->input('body');

        $v = validator($ticket->toArray(), [
            'user_id'     => 'required|exists:users,id',
            'category_id' => 'required|exists:ticket_categories,id',
            'priority_id' => 'required|exists:ticket_priorities,id',
            'subject'     => 'required',
            'body'        => 'required',
        ]);

        if ($v->fails()) {
            return to_route('tickets.create')
                ->withInput()
                ->withErrors($v->errors());
        }

        $ticket->save();

        return to_route('tickets.show', ['id' => $ticket->id])
            ->withSuccess(trans('ticket.updated-success'));
    }

    /**
     * Remove the specified resource from storage.
     */
    final public function destroy(Request $request, int $id): \Illuminate\Http\RedirectResponse
    {
        $ticket = Ticket::findOrFail($id);
        $user = $request->user();
        abort_unless($user->group->is_modo, 403);

        $ticket->comments()->delete();
        TicketAttachment::where('ticket_id', '=', $id)->delete();
        $ticket->delete();

        return to_route('tickets.index')
            ->withSuccess(trans('ticket.deleted-success'));
    }

    final public function assign(Request $request, int $id): \Illuminate\Http\RedirectResponse
    {
        $ticket = Ticket::findOrFail($id);
        $user = $request->user();
        abort_unless($user->group->is_modo, 403);

        $ticket->staff_id = $request->input('user_id');
        $ticket->staff_read = 0;

        $v = validator($ticket->toArray(), [
            'user_id' => 'required|exists:users,id',
        ]);

        if ($v->fails()) {
            return to_route('tickets.show', ['id' => $ticket->id])
                ->withErrors($v->errors());
        }

        $ticket->save();

        return to_route('tickets.show', ['id' => $ticket->id])
            ->withSuccess(trans('ticket.assigned-success'));
    }

    final public function unassign(Request $request, int $id): \Illuminate\Http\RedirectResponse
    {
        $ticket = Ticket::findOrFail($id);
        $user = $request->user();
        abort_unless($user->group->is_modo, 403);

        $ticket->staff_id = null;
        $ticket->save();

        return to_route('tickets.show', ['id' => $ticket->id])
            ->withSuccess(trans('ticket.unassigned-success'));
    }

    final public function close(Request $request, int $id): \Illuminate\Http\RedirectResponse
    {
        $ticket = Ticket::findOrFail($id);
        $user = $request->user();
        abort_unless($user->group->is_modo || $user->id == $ticket->user_id, 403);

        $ticket->closed_at = now();
        $ticket->save();

        return to_route('tickets.show', ['id' => $ticket->id])
            ->withSuccess(trans('ticket.closed-success'));
    }
}

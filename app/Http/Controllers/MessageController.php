<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;

class MessageController extends Controller
{
    /**
     * Show the message creation form for a student or organization.
     * Accepts either student_profile_id or organization_id as input.
     */
    public function create(Request $request)
    {
    $studentId = $request->input('student_id');
        $organizationId = $request->input('organization_id');
        $student = null;
        $organization = null;
        if ($studentId) {
            $student = \App\Models\Student::find($studentId);
            if (!$student) abort(404, 'Student not found');
        }
        if ($organizationId) {
            $organization = \App\Models\Organization::find($organizationId);
            if (!$organization) abort(404, 'Organization not found');
        }
        return view('messages.create', compact('student', 'organization'));
    }

    /**
     * Store a new message and redirect with SweetAlert feedback.
     */
    public function store(Request $request)
    {
        $request->validate([
            'recipient_type' => ['required', 'in:student,organization'],
            'recipient_id'   => ['required', 'integer'],
            'subject'        => ['required', 'string', 'max:255'],
            'body'           => ['required', 'string'],
        ]);

        $message = new Message();
        $message->sender_id = auth()->id();
        $message->recipient_type = $request->recipient_type;
        $message->recipient_id = $request->recipient_id;
        $message->subject = $request->subject;
        $message->body = $request->body;
        $message->save();

        // Redirect with SweetAlert feedback (to be handled in the view)
        return redirect()->back()->with([
            'swal' => [
                'icon' => 'success',
                'title' => 'Message Sent!',
                'text' => 'Your message has been sent successfully.'
            ]
        ]);
    }
}

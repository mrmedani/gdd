<?php
require "vendor/autoload.php";
$app = require_once "bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use App\Domains\AI\Support\AiSessionService;
use App\Domains\AI\Models\AiConversation;
// Reproduce the bug: user "changes the page" (starts chatting), then navigation.
// Scenario: startNew() is called -> Cache pointer forgotten. Then user sends a message
// -> activeMessages() creates a NEW conversation (ensureActive). This new conversation
// may be EMPTY in the drawer IF the user navigates without sending... OR
// The bug: "je change la page la conversation disparaît" = when switching page (open
// another session), the CURRENT active conversation gets archived... but the LIST shows it.
// ACTUAL BUG REPORT: after navigating away, the conversation "disparaît" (probably empty
// activeMessages) BUT remains in the sessions list = stale empty AiConversation rows.
$admin = App\Models\User::where("email", "admin@chronorex.ma")->first();
auth()->login($admin);
$uid = $admin->id;
// clean slate
AiConversation::where("user_id", $uid)->delete();
Illuminate\Support\Facades\Cache::forget(AiSessionService::activeKey($uid));
// 1. startNew
AiSessionService::startNew($uid);
// 2. send message -> ensureActive creates row + append
AiSessionService::append($uid, "Q test", "R test");
$id = AiSessionService::activeId($uid);
echo "session id=$id, list=".count(AiSessionService::listFor($uid))."\n";
// 3. User navigates to ANOTHER session (no messages) — openSession
AiSessionService::open($uid, $id); // reload active
// 4. NOW user startNew() again: pointer forgotten. No new row was created...
AiSessionService::startNew($uid);
echo "after startNew: activeId=".json_encode(AiSessionService::activeId($uid)).", list=".count(AiSessionService::listFor($uid))."\n";
$dupes = AiConversation::where("user_id", $uid)->get();
foreach ($dupes as $d) {
    echo "- id={$d->id} title='{$d->title}' messages=".count($d->messages ?? [])."\n";
}

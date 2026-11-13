<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Platform\AdminAiThread;
use App\Services\Admin\Ai\AdminAiActionService;
use App\Services\Admin\Ai\AdminAiAssistantService;
use App\Services\Admin\Ai\AdminAiDataService;
use App\Services\Admin\Ai\AdminAiOperationsService;
use App\Services\Admin\Ai\AdminAiPermissionService;
use App\Services\Admin\Ai\AdminAiTranslationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class AdminAiAssistantController extends Controller
{
    public function status(Request $request, AdminAiAssistantService $assistant)
    {
        return response()->json(['configured' => $assistant->configured(), 'provider' => 'gemini', 'model' => $assistant->model(),
            'allowed_actions' => app(AdminAiPermissionService::class)->actions($request->attributes->get('ai_admin')),
            'read_operations' => array_values(array_filter(array_merge(['products'], AdminAiOperationsService::OPERATIONS), fn ($op) => app(AdminAiPermissionService::class)->canRead($request->attributes->get('ai_admin'), $op))),
            'actions_enabled' => count(app(AdminAiPermissionService::class)->actions($request->attributes->get('ai_admin'))) > 0]);
    }

    public function chat(Request $request, AdminAiAssistantService $assistant)
    {
        $v = $request->validate([
            'message' => ['required', 'string', 'max:3000'],
            'thread_id' => ['nullable', 'uuid'],
            'language' => ['required', 'in:auto,Tagalog,English,Taglish'],
        ]);
        abort_if(trim($v['message']) === '', 422, 'Please enter a message.');
        abort_unless($assistant->configured(), 503, 'Hindi makita ang GEMINI_API_KEY. Check the existing Gemini configuration, then run php artisan optimize:clear.');
        $admin = $request->attributes->get('ai_admin');
        $lock = Cache::lock('admin-ai-chat:'.$admin->id, 120);
        abort_unless($lock->get(), 409, 'May kasalukuyang request. Hintaying matapos.');
        try {
            $thread = ! empty($v['thread_id'])
                ? AdminAiThread::query()->where('admin_account_id', $admin->id)->findOrFail($v['thread_id'])
                : AdminAiThread::query()->create(['id' => (string) Str::uuid(), 'admin_account_id' => $admin->id, 'messages' => []]);

            return response()->json($assistant->reply($admin, $thread, trim($v['message']), $v['language']));
        } finally {
            $lock->release();
        }
    }

    public function translate(Request $request, AdminAiTranslationService $translator)
    {
        $v = $request->validate([
            'language' => ['required', 'in:English,Tagalog,Taglish'],
            'messages' => ['required', 'array', 'min:1', 'max:8'],
            'messages.*.id' => ['required', 'integer', 'min:0', 'distinct'],
            'messages.*.text' => ['required', 'string', 'max:6000'],
        ]);
        abort_if(array_sum(array_map(fn ($m) => mb_strlen($m['text']), $v['messages'])) > 16000, 422, 'Translate a smaller batch of messages.');

        return response()->json(['messages' => $translator->translate($v['messages'], $v['language'])]);
    }

    public function decide(Request $request, string $action, AdminAiActionService $actions)
    {
        $v = $request->validate(['confirm' => ['required', 'boolean']]);

        return response()->json($actions->decide($request, $request->attributes->get('ai_admin'), $action, $v['confirm']));
    }

    public function alerts(Request $request, AdminAiDataService $data)
    {
        return response()->json($data->run('action_center', [], $request->attributes->get('ai_admin')));
    }

    public function read(Request $request, AdminAiDataService $data)
    {
        $v = $request->validate(['operation' => ['required', 'string', 'max:40'], 'filters' => ['present', 'array']]);

        try {
            return response()->json($data->run($v['operation'], $v['filters'], $request->attributes->get('ai_admin')));
        } catch (\InvalidArgumentException $error) {
            abort(422, 'Unsupported operation or filters.');
        }
    }

    public function prepare(Request $request, AdminAiActionService $actions)
    {
        return response()->json(['proposal' => $actions->prepare($request->attributes->get('ai_admin'), $request->all())]);
    }

    public function forget(Request $request, string $thread)
    {
        AdminAiThread::query()->where('admin_account_id', $request->attributes->get('ai_admin')->id)->findOrFail($thread)->delete();

        return response()->json(['ok' => true]);
    }
}

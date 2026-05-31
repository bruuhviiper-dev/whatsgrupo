<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendNewGroupPushJob;
use App\Mail\GroupApprovedMail;
use App\Mail\GroupRejectedMail;
use App\Models\Category;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

/**
 * Controller para moderação de grupos no painel de administração.
 * Permite listagem com filtros, aprovação, rejeição com motivo e exclusão.
 */
class GroupModerationController extends Controller
{
    // -------------------------------------------------------------------------
    // Listagem de todos os grupos com filtros
    // -------------------------------------------------------------------------

    public function index(Request $request)
    {
        $query = Group::with('category')->orderBy('created_at', 'desc');

        // Filtro por status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filtro por categoria
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Busca por nome
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filtro por tag de apostas/gambling
        if ($request->filled('gambling')) {
            $query->where('is_gambling', (bool) $request->gambling);
        }

        $groups     = $query->paginate(20)->withQueryString();
        $categories = Category::ordered()->get();

        return view('admin.groups.index', compact('groups', 'categories'));
    }

    // -------------------------------------------------------------------------
    // Listagem somente de grupos pendentes
    // -------------------------------------------------------------------------

    public function pending()
    {
        $groups     = Group::with('category')
            ->where('status', 'pending')
            ->orderBy('created_at', 'asc')
            ->paginate(20);
        $categories = Category::ordered()->get();

        return view('admin.groups.index', compact('groups', 'categories'));
    }

    // -------------------------------------------------------------------------
    // Aprovar grupo
    // -------------------------------------------------------------------------

    public function approve(Group $group)
    {
        $group->update(['status' => 'approved']);

        // Dispara a notificação push para a categoria do grupo
        SendNewGroupPushJob::dispatch($group);

        // Envia e-mail de aprovação se o grupo tiver e-mail cadastrado
        if ($group->submitter_email) {
            try {
                Mail::to($group->submitter_email)->send(new GroupApprovedMail($group));
            } catch (\Exception $e) {
                Log::warning('[GroupModerationController] Falha ao enviar e-mail de aprovação: ' . $e->getMessage());
            }
        }

        return back()->with('success', "✅ Grupo \"{$group->name}\" aprovado com sucesso!");
    }

    // -------------------------------------------------------------------------
    // Rejeitar grupo com motivo
    // -------------------------------------------------------------------------

    public function reject(Request $request, Group $group)
    {
        $request->validate([
            'reason' => 'required|string|min:10|max:500',
        ], [
            'reason.required' => 'Informe o motivo da rejeição.',
            'reason.min'      => 'O motivo deve ter pelo menos 10 caracteres.',
        ]);

        $group->update(['status' => 'rejected']);

        // Envia e-mail de rejeição com o motivo
        if ($group->submitter_email) {
            try {
                Mail::to($group->submitter_email)->send(new GroupRejectedMail($group, $request->reason));
            } catch (\Exception $e) {
                Log::warning('[GroupModerationController] Falha ao enviar e-mail de rejeição: ' . $e->getMessage());
            }
        }

        return back()->with('success', "❌ Grupo \"{$group->name}\" rejeitado.");
    }

    // -------------------------------------------------------------------------
    // Alternar tag de apostas/gambling (visível só no dashboard)
    // -------------------------------------------------------------------------

    public function toggleGambling(Group $group)
    {
        $newValue = ! $group->is_gambling;
        $group->update(['is_gambling' => $newValue]);

        $label = $group->name;

        if ($newValue) {
            return back()->with('success', '🎲 Tag de apostas ativada no grupo "' . $label . '". Este grupo não poderá ser impulsionado.');
        }

        return back()->with('success', '✅ Tag de apostas removida do grupo "' . $label . '".');
    }

    // -------------------------------------------------------------------------
    // Excluir grupo permanentemente
    // -------------------------------------------------------------------------

    public function destroy(Group $group)
    {
        // Remove a imagem do storage caso exista
        if ($group->image_path && Storage::disk('public')->exists($group->image_path)) {
            Storage::disk('public')->delete($group->image_path);
        }

        $name = $group->name;
        $group->delete();

        return back()->with('success', "🗑️ Grupo \"{$name}\" excluído permanentemente.");
    }
}

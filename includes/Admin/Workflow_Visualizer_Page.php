<?php
/** Workflow visualizer admin page. @package OBEngine\Admin */
namespace OBEngine\Admin;
use OBEngine\Support\Capabilities;
use OBEngine\Support\View;
use OBEngine\Workflow\Workflow_Graph;
defined( 'ABSPATH' ) || exit;
final class Workflow_Visualizer_Page {
	public function render(): void { if ( ! current_user_can( Capabilities::MANAGE ) ) { wp_die( esc_html__( 'You do not have permission to view OBE Workflow.', 'ob-engine' ) ); } $graph=new Workflow_Graph(); ?>
		<div class="wrap ob-engine-wrap ob-engine-page-form">
			<?php View::heading( __( 'Workflow', 'ob-engine' ) ); ?>
			<div class="notice notice-info inline"><p><?php esc_html_e( 'This visualizes OBE’s review-first workflow. It does not run agents, call AI, write posts, or publish.', 'ob-engine' ); ?></p></div>
			<div class="ob-engine-card"><h2><?php esc_html_e( 'Workflow Nodes', 'ob-engine' ); ?></h2><table class="widefat striped"><thead><tr><th><?php esc_html_e('Node','ob-engine'); ?></th><th><?php esc_html_e('Purpose','ob-engine'); ?></th><th><?php esc_html_e('Input','ob-engine'); ?></th><th><?php esc_html_e('Output','ob-engine'); ?></th><th><?php esc_html_e('Library type','ob-engine'); ?></th><th><?php esc_html_e('Human review','ob-engine'); ?></th><th><?php esc_html_e('AI call','ob-engine'); ?></th><th><?php esc_html_e('WordPress write','ob-engine'); ?></th><th><?php esc_html_e('Risk','ob-engine'); ?></th><th><?php esc_html_e('Activity event','ob-engine'); ?></th></tr></thead><tbody><?php foreach($graph->nodes() as $node): $n=$node->to_array(); ?><tr><td><strong><?php echo esc_html($n['label']); ?></strong></td><td><?php echo esc_html($n['description']); ?></td><td><?php echo esc_html($n['input']); ?></td><td><?php echo esc_html($n['output']); ?></td><td><?php echo esc_html($n['library_type'] ?: '—'); ?></td><td><?php echo esc_html($n['requires_human_review']?'yes':'no'); ?></td><td><?php echo esc_html($n['calls_ai_provider']?'yes':'no'); ?></td><td><?php echo esc_html($n['writes_wordpress']?'yes':'no'); ?></td><td><?php echo esc_html($n['risk_level']); ?></td><td><?php echo esc_html(implode(', ', $n['activity_events'])); ?></td></tr><?php endforeach; ?></tbody></table></div>
			<div class="ob-engine-card"><h2><?php esc_html_e('Flow Edges','ob-engine'); ?></h2><ol><?php foreach($graph->edges() as $edge): ?><li><strong><?php echo esc_html($edge->get_label()); ?></strong> — <?php echo esc_html($edge->get_gate()); ?></li><?php endforeach; ?></ol></div>
			<div class="ob-engine-card"><h2><?php esc_html_e('Risk Summary','ob-engine'); ?></h2><ul><?php foreach($graph->risk_summary() as $risk=>$count): ?><li><?php echo esc_html($risk . ': ' . $count); ?></li><?php endforeach; ?></ul></div>
			<div class="ob-engine-card"><h2><?php esc_html_e('Agent SDK / Workflow Prototype','ob-engine'); ?></h2><p><?php esc_html_e('This visualizer is Agent-SDK-ready workflow metadata/prototype. It does not add actual Agent SDK runtime, autonomous execution, tools, background jobs, or workflow execution.', 'ob-engine'); ?></p><p><?php esc_html_e('Future agent/tool execution belongs to Workflow Pro/private tooling unless explicitly approved later.', 'ob-engine'); ?></p><pre><?php echo esc_html($graph->to_json()); ?></pre></div>
			<div class="ob-engine-card"><h2><?php esc_html_e('QC Checklist','ob-engine'); ?></h2><p><?php esc_html_e('Review docs/QC_CHECKLIST.md before release. Do not publish before all pass.', 'ob-engine'); ?></p><p><a class="button button-secondary" href="<?php echo esc_url(admin_url('admin.php?page='.Admin_Menu::LIBRARY_SLUG)); ?>"><?php esc_html_e('Back to Library','ob-engine'); ?></a></p></div>
			<div class="ob-engine-card"><h2><?php esc_html_e('Release Gate','ob-engine'); ?></h2><p><?php esc_html_e('v1.0 requires manual QC, workflow visualizer review, write gate verification, and public/private boundary verification.', 'ob-engine'); ?></p></div>
		</div><?php }
}

<?php
auth_reauthenticate();
access_ensure_global_level( plugin_config_get( 'admin_threshold' ) );

layout_page_header( plugin_lang_get( 'manage_reports' ) );
layout_page_begin();

print_manage_menu();
print_custom_report_config_menu( 'manage_reports_overview_page' );

# Mostrar mensajes de éxito o error
if ( gpc_get_bool( 'success', false ) ) {
    echo '<div class="alert alert-success">' . plugin_lang_get( 'update_successful' ) . '</div>';
} else if ( gpc_get_bool( 'invalid_keywords', false ) ) {
    echo '<div class="alert alert-danger">' . plugin_lang_get( 'query_invalid_keywords' ) . '</div>';
}

$t_all_reports = custom_reports_get_all();
?>

<div class="col-md-12 col-xs-12">
    <div class="space-10"></div>
    <div class="widget-box widget-color-blue2">
        <div class="widget-header">
            <h4 class="widget-title lighter">
                <i class="ace-icon fa fa-list"></i>
                <?php echo plugin_lang_get( 'manage_reports' ) ?>
            </h4>
            <div class="widget-toolbar no-border">
                <form method="post" action="<?php echo plugin_page( 'manage_reports_page' ) ?>">
                    <button type="submit" class="btn btn(xs btn-primary btn-white btn-round">
                        <i class="fa fa-plus"></i> <?php echo plugin_lang_get( 'add_new_report' ) ?>
                    </button>
                </form>
            </div>
        </div>
        <div class="widget-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-condensed table-hover">
                    <thead>
                        <tr>
                            <th><?php echo plugin_lang_get( 'report_name' ) ?></th>
                            <th><?php echo plugin_lang_get( 'view_threshold' ) ?></th>
                            <th><?php echo plugin_lang_get( 'query' ) ?></th>
                            <th class="center"><?php echo lang_get( 'actions' ) ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ( $t_all_reports as $row ) { ?>
                        <tr>
                            <td><?php echo string_display_line($row['name']) ?></td>
                            <td><?php echo get_enum_element( 'access_levels', $row['view_threshold'] ) ?></td>
                            <td><code class="small"><?php echo string_shorten( $row['query'], 100 ) ?></code></td>
                            <td class="center">
                                <div class="btn-group">
                                    <form method="post" action="<?php echo plugin_page( 'manage_reports_page' ) ?>" style="display:inline">
                                        <input type="hidden" name="report_id" value="<?php echo (int)$row['id'] ?>" />
                                        <button type="submit" class="btn btn-xs btn-info btn-white btn-round">
                                            <?php echo lang_get( 'edit' ) ?>
                                        </button>
                                    </form>
                                    <form method="post" action="<?php echo plugin_page( 'manage_reports_delete' ) ?>" style="display:inline">
                                        <?php echo form_security_field( 'plugin_CustomReports_delete' ) ?>
                                        <input type="hidden" name="report_id" value="<?php echo (int)$row['id'] ?>" />
                                        <button type="submit" class="btn btn-xs btn-danger btn-white btn-round">
                                            <?php echo lang_get( 'remove_link' ) ?>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php layout_page_end(); ?>
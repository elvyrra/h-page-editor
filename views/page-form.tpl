{assign name="content"}
    <div class="pull-right">
        {{ $form->fieldsets['submits'] }}
    </div>
    <div role="tabpanel" id="page-form-tabs" >  
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#page-form-tab-global" role="tab" data-toggle="tab">{text key="page-editor.page-form-global-legend"}</a></li>
            <li role="presentation"><a href="#page-form-tab-menu" role="tab" data-toggle="tab">{text key="page-editor.page-form-menu-legend"}</a></li>            
        </ul>
        
        <!-- Tab panes -->
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="page-form-tab-global">
                {{ $form->fieldsets['global'] }}
            </div>

            <div role="tabpanel" class="tab-pane" id="page-form-tab-menu">
                {{ $form->fieldsets['menu'] }}
            </div>
        </div>
    </div>

    
    
{/assign}

{form id="{$form->id}" content="{$content}"}
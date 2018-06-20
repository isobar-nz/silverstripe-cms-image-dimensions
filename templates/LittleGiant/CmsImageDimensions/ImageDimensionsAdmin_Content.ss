<div class="cms-content flexbox-area-grow {$BaseCSSClasses}" data-layout-type="border" data-pjax-fragment="Content">
    {$Tools}

    <div class="toolbar toolbar--north cms-content-header vertical-align-items">
        <div class="cms-content-header-info flexbox-area-grow vertical-align-items">
            <div class="breadcrumbs-wrapper flexbox-area-grow" data-pjax-fragment="Breadcrumbs">
                <span class="cms-panel-link crumb last">Image Dimensions</span>
            </div>
        </div>
    </div>
    <div class="panel panel--padded panel--scrollable flexbox-area-grow cms-panel-padded">
        <table class="table grid-field__table">
            <thead>
            <tr class="grid-field__title-row">
                <td>Name</td>
                <td>Description</td>
                <td>Allowed Extensions</td>

                <td>Dimensions (W&times;H)</td>
                <% if $ShowConstraintEnforcement %>
                    <td>Min. Dimensions Enforced</td>
                <% end_if %>

                <td>Aspect Ratio (W:H)</td>
                <% if $ShowConstraintEnforcement %>
                    <td>Aspect Ratio Enforced</td>
                <% end_if %>
            </tr>
            </thead>
            <tbody class="ss-gridfield-items">
                <% loop $ImageDefinitions %>
                <tr class="ss-gridfield-item">
                    <td>{$Name}</td>
                    <td>{$Description}</td>
                    <td>{$AllowedExtensionsNice}</td>

                    <td>{$DimensionsNice}</td>
                    <% if $Up.ShowConstraintEnforcement %>
                        <td>{$ValidateDimensions.Nice}</td>
                    <% end_if %>

                    <td>{$AspectRatioNice}</td>
                    <% if $Up.ShowConstraintEnforcement %>
                        <td>{$ValidateAspectRatio.Nice}</td>
                    <% end_if %>
                </tr>
                <% end_loop %>
            </tbody>
        </table>
    </div>
</div>

<!-- Edit Custom Policy Modal -->
<div class="modal fade" id="edit_custom_policy_{{ $policy->id }}" tabindex="-1" aria-labelledby="editCustomPolicyLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editCustomPolicyLabel">Edit Custom Policy</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                            <form action="{{ route('customupdate', $policy->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="mb-3">
                                        <label for="policy_name" class="form-label">Policy Name</label>
                                        <input type="text" class="form-control" id="policy_name" name="policy_name" value="{{ $policy->policy_name }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="days" class="form-label">Days</label>
                                        <input type="number" class="form-control" id="days" name="days" value="{{ $policy->policy_days }}" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Update</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <script>
    $(document).ready(function () {
        
        // Move all selected items to 'customleave_to[]'
        $('#customleave_select_rightSelected').click(function (e) {
            var selectedOptions = $('#customleave_select option:selected');
            $('#customleave_select_to').append(selectedOptions);
        });

        // Move selected items back to 'customleave_from[]'
        $('#customleave_select_leftSelected').click(function (e) {
            var selectedOptions = $('#customleave_select_to option:selected');
            $('#customleave_select').append(selectedOptions);
        });

        // Move all items to 'customleave_to[]'
        $('#customleave_select_rightAll').click(function (e) {
            var allOptions = $('#customleave_select option');
            $('#customleave_select_to').append(allOptions);
        });

        // Move all items back to 'customleave_from[]'
        $('#customleave_select_leftAll').click(function (e) {
            var allOptions = $('#customleave_select_to option');
            $('#customleave_select').append(allOptions);
        });
    });
</script>
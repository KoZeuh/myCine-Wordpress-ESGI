jQuery(document).ready(function($) {
    $('#add-showtime').click(function() {
        $('#showtimes-container').append('<div class="showtime"><input type="text" name="showtime_day[]" placeholder="Jour" /><input type="time" name="showtime_time[]" placeholder="Heure" /><button type="button" class="remove-showtime">Supprimer</button></div>');
    });
    $(document).on('click', '.remove-showtime', function() {
        $(this).closest('.showtime').remove();
    });
    
    $('#admin-add-showtime').click(function() {
        $('#admin-showtimes-container').append('<div class="showtime"><input type="text" name="showtime_day[]" placeholder="Jour" /><input type="time" name="showtime_time[]" placeholder="Heure" /><button type="button" class="remove-showtime">Supprimer</button></div>');
    });
    $(document).on('click', '.remove-showtime', function() {
        $(this).closest('.showtime').remove();
    });
});

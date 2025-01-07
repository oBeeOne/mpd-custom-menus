jQuery(document).ready(function($) {

    // Rendre le tbody sortable
    $('#mpd_menu_items_tbody').sortable({
      axis: 'y',
      handle: '.mpd-drag-handle'
    });
  
    // Bouton "Ajouter un élément"
    $('#mpd_add_menu_item').on('click', function(e) {
      e.preventDefault();
      addNewRow();
    });
  
    function addNewRow(data = {}) {
      // data contient {title, href, class}
      const titleVal = data.title || '';
      const hrefVal  = data.href  || '';
      const classVal = data.class || '';
  
      const newRow = `
        <tr class="mpd-menu-item-row">
          <td class="mpd-drag-handle" style="cursor:move;">&#x2630;</td>
          <td><input type="text" name="mpd_item_title[]" value="${titleVal}" placeholder="Titre" style="width:100%;" /></td>
          <td><input type="text" name="mpd_item_href[]" value="${hrefVal}" placeholder="/lien-relatif" style="width:100%;" /></td>
          <td><input type="text" name="mpd_item_class[]" value="${classVal}" placeholder="Classe CSS" style="width:100%;" /></td>
          <td><button type="button" class="button-link-delete mpd-remove-item" style="color:red;">X</button></td>
        </tr>
      `;
      $('#mpd_menu_items_tbody').append(newRow);
    }
  
    // Supprimer un élément
    $(document).on('click', '.mpd-remove-item', function(e){
      e.preventDefault();
      $(this).closest('.mpd-menu-item-row').remove();
    });
  
  });
  
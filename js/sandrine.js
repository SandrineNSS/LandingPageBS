function controleSaisieUser(type) {
  if (event instanceof KeyboardEvent) {
    // it is a keyboard event!
    if (event.ctrlKey) return true;

    var oTarget = event.target ? event.target : event.srcElement;
    var maxLength = oTarget.maxLength;
    var start = oTarget.selectionStart;
    var end = oTarget.selectionEnd;

    if (
      type == 5 &&
      event.key.length == 1 &&
      ((event.key >= "a" && event.key <= "z") ||
        (event.key >= "A" && event.key <= "Z")) &&
      (maxLength == -1 || oTarget.value.length < maxLength || end - start > 0)
    ) {
      remplaceSaisiePar(event.key.toUpperCase(), event, oTarget);
      return false;
    } else if (
      type == 4 &&
      (event.code == "NumpadDecimal" ||
        event.code == "KeyM" ||
        event.code == "Comma") &&
      (maxLength == -1 || oTarget.value.length < maxLength || end - start > 0)
    ) {
      remplaceSaisiePar(".", event, oTarget);
      return false;
    }
    // NE PAS METTRE DE ELSE
    if (
      ((event.code.startsWith("Digit") && event.code.length == 6) ||
        (event.code.startsWith("Numpad") && event.code.length == 7)) &&
      (maxLength == -1 || oTarget.value.length < maxLength || end - start > 0)
    ) {
      var codeKey = event.code.substr(event.code.length - 1);

      remplaceSaisiePar(codeKey, event, oTarget);
      return false;
    }

    var key = event.key;
    if (
      (((key >= "0" && key <= "9") ||
        key == "/" ||
        key == ":" ||
        (key == ":" && type == 3) ||
        ((key == "A" || key == "B" || key == "a" || key == "b") &&
          start == 6 &&
          type == 2)) &&
        (maxLength == -1 ||
          oTarget.value.length < maxLength ||
          end - start > 0)) ||
      [
        "Delete",
        "ArrowLeft",
        "ArrowRight",
        "Backspace",
        "Enter",
        "Tab",
      ].includes(key)
    )
      return true;
  }
  return false;
}

function remplaceSaisiePar(codeKey, event, oTarget) {
  var start = oTarget.selectionStart;
  var end = oTarget.selectionEnd;
  var oldValue = oTarget.value;

  // replace point and change input value
  var newValue = oldValue.slice(0, start) + codeKey + oldValue.slice(end);
  oTarget.value = newValue;

  // replace cursor
  oTarget.selectionStart = oTarget.selectionEnd = start + 1;

  event.preventDefault();
}

class ClassWorkFlow {
  static changeStatut(obj, idContactClient) {
    $.ajax({
      url: "traitement_status.php",
      data: {
        fct: "changeStatut",
        idContactClient: idContactClient,
        statut: obj.value,
      },
      type: "post",
      success: function (myObj) {
        if (myObj.success) {
          console.log("Statut mis à jour avec succès");
        } else {
          console.log("Erreur lors de la mise à jour du statut");
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
        console.log("Erreur AJAX: " + textStatus + ", " + errorThrown);
      },
    });
  }

  static changeStatutDemande(obj, idContactClient) {
    $.ajax({
      url: "traitement_status.php", 
      data: {
        fct: "changeStatutDemande",
        idxContactClient: idContactClient,
        statut: obj.value,
      },
      type: "post",
      success: function (myObj) {
        // Gére la réussite de la requête 
      },
      error: function (jqXHR, textStatus, errorThrown) {
        // Gére les erreurs AJAX 
      },
    });
  }
}


<?php

namespace App\Entity;

class Costanti {
    const LIVE_SITE = "https://epmusic.mister-wolf.it/";
    const EMAIL_AMMINISTRATORE = ['naps8787@gmail.com'];
    const EMAIL_INFO = "naps8787@gmail.com"; //da dove partono le email (from)
    const EMAIL_INFO_RICHIESTA = "naps8787@gmail.com"; //dell'audio

    // TEMPLATE EMAIL / LOGIN
    const REGISTRATION = "Grazie per il tuo interesse nei confronti dell’iniziativa! Abbiamo preso in carico la tua richiesta.<br>
Riceverai una e-mail di conferma all’indirizzo  <strong><username></strong>. con le credenziali per accedere alla tua Area Riservata<br>
Aggiungi l’email <strong>info@crm.mister-wolf.it</strong> ai tuoi contatti o contrassegna il messaggio come NON SPAM, per evitare che i nostri messaggi finiscano nella posta indesiderata. <br><br>
Per qualunque richiesta scrivici a <a href='mailto:info@crm.mister-wolf.it'>info@crm.mister-wolf.it</a>.";
    const ACCOUNT_NO_CONFERMATO = "Attenzione, il tuo account non risulta confermato.";
    const ACCOUNT_RIFIUTATO = "Attenzione, il tuo account non è stato approvato. Puoi effettuare una nuova registrazione.";
    const ACCOUNT_CONFERMATO = "Grazie per aver confermato il tuo account. Ora puoi accedere al sistema con i tuoi dati.";
    const ACCOUNT_GIA_CONFERMATO = "Il tuo account risulta già confemato. Inserisci i tuoi dati per accedere al sistema.";
    const ERRORE_REGISTRAZIONE = "Attenzione, la tua email è già presente nel sistema!<br>Se non ricordi la password <a href='recupera-password'>puoi recuperarla</a>";
    const ERRORE_REGISTRAZIONE_IN_APPROVAZIONE = "Attenzione, sei in attesa di approvazione. Non puoi accedere nè recuperare la password.";
    const ERRORE_RIPETI_PASSWORD = "Attenzione, le password inserite non coincidono.";
    const ERRORE_LOGIN = "Username o password non valide";


    //MESSAGGI TOAST
    const TOAST_ERRORE = "Errore";
    const TOAST_ERRORE_DELETE_TITLE = "Impossibile eliminare";
    const TOAST_ERRORE_DELETE_TEXT = "Il cliente <strong>{elemento}</strong> è associato ad uno o più lavori.";
    const TOAST_ERRORE_DELETE2_TEXT = "Il servizio <strong>{elemento}</strong> è associato ad uno o più lavori.";
    const TOAST_CREATE_TITLE = "Elemento creato";
    const TOAST_CREATE_TEXT = "<strong>{elemento}</strong> è stato creato con successo.";
    const TOAST_UPDATE_TITLE = "Elemento aggiornato";
    const TOAST_UPDATE_TEXT = "<strong>{elemento}</strong> è stato modificato con successo.";
    const TOAST_DELETE_TITLE = "Elemento eliminato";
    const TOAST_DELETE_TEXT = "<strong>{elemento}</strong> è stato eliminato con successo.";

    const TOAST_RICHIESTA_TITLE = "Richiesta inviata";
    const TOAST_RICHIESTA_TEXT = "La richiesta per il brano <strong>{elemento}</strong> è stata inviata con successo.";

}

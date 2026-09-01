(function() {
    if ( typeof window.suspendToggleInit === 'undefined' ) {
        window.suspendToggleInit = true;
        document.addEventListener( 'DOMContentLoaded', function() {
            const btn = document.getElementById( 'suspendtoggle-btn' );
            if ( !btn ) {
                return;
            }
            const statusEl = document.getElementById( 'suspendtoggle-status' );
            const bugId = btn.getAttribute( 'data-bug-id' );

            let basePath = '';
            if ( typeof config !== 'undefined' && config['short_path'] ) {
                basePath = config['short_path'].replace( /\/?$/, '/' );
            }
            const url = basePath + 'api/rest/index.php/plugins/SuspendToggle/toggle';

            function showStatus( msg, isError ) {
                statusEl.textContent = msg;
                statusEl.style.display = 'inline';
                if ( isError ) {
                    statusEl.classList.add( 'label-danger' );
                } else {
                    statusEl.classList.remove( 'label-danger' );
                }
            }

            btn.addEventListener( 'click', function() {
                btn.disabled = true;
                statusEl.style.display = 'none';

                fetch( url, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify( { bug_id: bugId } ),
                    credentials: 'same-origin'
                } )
                .then( function( res ) {
                    if ( !res.ok ) {
                        throw new Error( 'HTTP ' + res.status );
                    }
                    return res.json();
                } )
                .then( function( data ) {
                    if ( data.status === 'suspended' ) {
                        showStatus( btn.getAttribute( 'data-msg-suspended' ), false );
                    } else {
                        showStatus( btn.getAttribute( 'data-msg-resumed' ), false );
                    }
                    setTimeout( function() {
                        window.location.reload();
                    }, 1500 );
                } )
                .catch( function() {
                    showStatus( btn.getAttribute( 'data-msg-error' ), true );
                    btn.disabled = false;
                } );
            } );
        } );
    }
})();

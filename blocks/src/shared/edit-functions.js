
export function closeModal(ref) {
    if (ref.current) {
        ref.current.classList.remove('modal-open');
    }
}

export function getMyClubBookables( setPosts, selectPostLabel ) {
    const { apiFetch } = wp;

    apiFetch( { path: '/myclub/v1/bookables' } ).then(
        fetchedItems => {
            const postOptions = fetchedItems.results.map( post => ({
                label: post.name,
                value: post.id
            }));

            postOptions.unshift( selectPostLabel );

            setPosts( postOptions );
        }
    );
}

export function setHeight ( ref, className ) {
    setTimeout(() => {
        const elements = Array.from( ref.current.getElementsByClassName( className ) );
        const maxHeight = Math.max( ...elements.map(( element ) => element.offsetHeight) );

        elements.forEach((element) => {
            element.style.height = `${maxHeight}px`;
        });
    });
}

export function showMemberModal( ref, member, labels ) {
    if (ref.current) {
        const informationElement = ref.current.getElementsByClassName('information')[0];
        let output = '<div class="name">' + member.name + '</div>';

        if ( member.role || member.phone || member.email || member.age ) {
            output += '<table>';

            if ( member.role ) {
                output += `<tr><th>${labels.role}</th><td>${member.role}</td></tr>`;
            }

            if ( member.age ) {
                output += `<tr><th>${labels.age}</th><td>${member.age}</td></tr>`;
            }

            if ( member.email ) {
                output += `<tr><th>${labels.email}</th><td><a href="mailto:${member.email}">${member.email}</a></td></tr>`;
            }

            if ( member.phone ) {
                output += `<tr><th>${labels.phone}</th><td><a href="tel:${member.phone}">${member.phone}</a></td></tr>`;
            }

            output += '</table>';
        }

        informationElement.innerHTML = output;
        ref.current.classList.add('modal-open');
    }
}
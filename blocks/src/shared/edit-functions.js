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

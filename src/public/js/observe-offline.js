function renderOfflineCharacters(allCharacters) {
    const offlineCharacters = allCharacters
        .filter(c => !c.is_online)
        .sort((a, b) => {
            return serverDate(b.offline_at + " UTC") - serverDate(a.offline_at + " UTC");
        });

    offlineCharacters.forEach(character => {
        addRow(getTableIdForCharacterType(character.type), -1, character, true);
    });
}

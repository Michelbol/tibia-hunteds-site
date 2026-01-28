const input = document.getElementById('guild_name');
const link  = document.getElementById('guildLink');

const baseUrl = 'https://www.tibia.com/community/?subtopic=guilds&page=view&GuildName=';

input.addEventListener('input', function () {
    const value = this.value.trim();

    if (value.length === 0) {
        link.href = '#';
        link.classList.add('disabled');
        return;
    }

    const encodedGuild = encodeURIComponent(value);
    link.href = baseUrl + encodedGuild;
    link.classList.remove('disabled');
});

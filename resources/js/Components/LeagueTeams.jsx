export default LeagueTeams = ({leagues}) => {
    return (
        <div>
          <h2>Leagues with Teams</h2>
          {leagues.map((league) => (
            <div key={league.id}>
                <p>{league.name}</p>
                <ul>
                    {league.teams.map((team) => (
                        <li key={team.id}>{team.name}</li>
                    ))}
                </ul>
            </div>
          ))}
        </div>
    );
};
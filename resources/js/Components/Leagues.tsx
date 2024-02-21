import React, { useState, useEffect } from "react";
import { Button } from "@/components/ui/button"

interface League {
  id: number;
  name: string;
  teams: Team[];
}

interface Team {
  id: number;
  name: string;
  price: number;
}

interface SelectedTeams {
  [leagueId: string]: {
    [teamIndex: number]: string;
  };
}

interface LeaguesProps {
  leagues: League[];
  budget: number;
}

const Leagues: React.FC<LeaguesProps> = ({ leagues, budget }) => {
  const initialSelectedTeams: SelectedTeams = {};
  const [selectedTeams, setSelectedTeams] = useState<SelectedTeams>(
    initialSelectedTeams
  );

  const handleTeamSelection = (
    leagueId: string,
    teamIndex: number,
    teamId: string
  ) => {
    setSelectedTeams((prevTeams) => ({
      ...prevTeams,
      [leagueId]: {
        ...(prevTeams[leagueId] || {}),
        [teamIndex]: teamId,
      },
    }));

    toggleDisabled(leagueId, teamIndex, teamId);
  };

  const isValid = () => {
    const isTotalSpendValid = calculateTotalSpend() <= budget;

    const isTeamsCountValid =
      Object.values(selectedTeams).length === leagues.length &&
      Object.values(selectedTeams).every(
        (leagueSelection) =>
          leagueSelection && Object.keys(leagueSelection).length === 2
      );

    return isTotalSpendValid && isTeamsCountValid;
  };

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (isValid()) {
      console.log("Form Submitted Successfully!");
    } else {
      console.log(
        "Invalid Submission. Please check your selections and budget."
      );
    }
  };

  const calculateTotalSpend = () => {
    let newTotalSpend = 0;

    Object.entries(selectedTeams).forEach(([leagueId, leagueSelection]) => {
      Object.values(leagueSelection).forEach((teamId) => {
        const selectedLeague = leagues.find(
          (league) => league.id === parseInt(leagueId)
        );

        if (selectedLeague) {
          const selectedTeam = selectedLeague.teams.find(
            (team) => team.id === parseInt(teamId)
          );

          newTotalSpend += selectedTeam ? selectedTeam.price : 0;
        }
      });
    });

    return newTotalSpend;
  };

  const toggleDisabled = (leagueId: string, teamIndex: number, teamId: string) => {
    let selected = document.querySelectorAll(
      `select[name="teams[${leagueId}]"]`
    );

    selected.forEach((select) => {
      Array.from(select.options).forEach((option) => {
        option.disabled = false;
      });

      if (select.value !== teamId) {
        Array.from(select.options).forEach((option) => {
          if (option.value === teamId) {
            option.disabled = true;
          }
        });
      }
    });
  };

  return (
    <div className="container mx-auto mt-8 p-8 bg-white rounded shadow-lg">
      <h2 className="text-2xl font-semibold mb-4">
        Select 2 teams from each league
      </h2>
      {calculateTotalSpend()}
      <form onSubmit={handleSubmit}>
        {leagues.map((league) => (
          <div key={league.id} className="mb-8">
            <div className="bg-gray-200 p-4 rounded-t-lg">
              <h5 className="text-lg font-semibold">{league.name}</h5>
            </div>
            <div className="p-4 bg-white rounded-b-lg shadow-md">
              {Array.from({ length: 2 }, (_, index) => (
                <select
                  key={index}
                  className="form-select mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                  id={`team_${league.id}_${index}`}
                  name={`teams[${league.id}]`}
                  onChange={(e) =>
                    handleTeamSelection(league.id.toString(), index, e.target.value)
                  }
                  value={selectedTeams[league.id]?.[index] || ""}
                >
                  {/* Placeholder option */}
                  <option value="" disabled>
                    Select a team
                  </option>

                  {/* Dynamically generated team options */}
                  {league.teams.map((team) => (
                    <option key={team.id} value={team.id}>
                      {`${team.name} - £${team.price}m`}
                    </option>
                  ))}
                </select>
              ))}
            </div>
          </div>
        ))}

        <Button variant="dark" type="submit">Submit</Button>
      </form>
    </div>
  );
};

export default Leagues;
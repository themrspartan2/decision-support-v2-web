# -*- coding: utf-8 -*-
"""GroupMaker.ipynb

Automatically generated by Colaboratory.

Original file is located at
    https://colab.research.google.com/drive/1l579qKhrBrlRzitySIOJ-xgxxyK9W4WY
"""

!pip install ortools

from ortools.linear_solver import pywraplp


def main():
    # Data
    costs = [
      [3.93, 3.93],
      [2.63, 2.63],
      [3.63, 3.63],
      [3.33, 3.33],
      [2.57, 2.57],
      [2.80, 2.80]
    ]
    num_students = len(costs)
    num_groups = len(costs[0])

    # Solver
    # Create the mip solver with the SCIP backend.
    solver = pywraplp.Solver.CreateSolver('SCIP')

    if not solver:
        return

    # Variables
    # x[i, j] is an array of 0-1 variables, which will be 1
    # if student i is assigned to group j.
    x = {}
    for i in range(num_students):
        for j in range(num_groups):
            x[i, j] = solver.IntVar(0, 1, '')

    # Constraints
    # Each student is assigned to at most 1 group.
    for i in range(num_students):
        solver.Add(solver.Sum([x[i, j] for j in range(num_groups)]) <= 1)

    # Each group is assigned to exactly 3 students.
    for j in range(num_groups):
        solver.Add(solver.Sum([x[i, j] for i in range(num_students)]) == 3)

    # Objective
    objective_terms = []
    for i in range(num_students):
        for j in range(num_groups):
            objective_terms.append(costs[i][j] * x[i, j])
    solver.Minimize(solver.Sum(objective_terms))

    # Solve
    status = solver.Solve()

    # Print solution.
    if status == pywraplp.Solver.OPTIMAL or status == pywraplp.Solver.FEASIBLE:
        print(f'Total cost = {solver.Objective().Value()}\n')
        for i in range(num_students):
            for j in range(num_groups):
                # Test if x[i,j] is 1 (with tolerance for floating point arithmetic).
                if x[i, j].solution_value() > 0.5:
                    print(f'Student {i} assigned to group {j}.' +
                          f' Grade: {costs[i][j]}')
    else:
        print('No solution found.')


if __name__ == '__main__':
    main()